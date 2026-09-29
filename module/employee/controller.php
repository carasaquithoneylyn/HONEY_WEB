<?php
// Employee controller
require_once("../../include/initialize.php");
global $mydb;

/* Staff-only module. Without this, controller.php can be called straight
   from the address bar by anyone, logged in or not. */
if (!isset($_SESSION['UID'])) {
	redirect(WEB_ROOT."login.php");
	exit;
}

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'save' :
		doSave();
		break;

	case 'delete' :
		doDelete();
		break;
}


/* ---------------------------------------------------------------------
   Validate a picked photo WITHOUT touching disk yet.

   The browser-supplied filename is not evidence of anything, so the real
   file header is read with getimagesize() and the extension we save with
   comes from the detected MIME type, not from the upload's name.

   Returns:  ''     nothing was picked (photo is optional)
             false  something was picked but it is not a usable image
             'jpg' / 'png' / 'gif' / 'webp'  the extension to save it as
   --------------------------------------------------------------------- */
function validate_employee_photo($file) {

	if (!isset($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
		return '';
	}
	if ($file['error'] !== UPLOAD_ERR_OK) {
		return false;
	}
	if ($file['size'] > 2 * 1024 * 1024) {
		return false;
	}

	$info = @getimagesize($file['tmp_name']);
	if ($info === false) {
		return false;
	}

	$allowed = array(
		'image/jpeg' => 'jpg',
		'image/png'  => 'png',
		'image/gif'  => 'gif',
		'image/webp' => 'webp'
	);

	return isset($allowed[$info['mime']]) ? $allowed[$info['mime']] : false;
}


/* ---------------------------------------------------------------------
   Move the already-validated upload into uploads/employees/ and return
   the path to store in PhotoPath, or false if the move failed.
   --------------------------------------------------------------------- */
function store_employee_photo($file, $ext, $oldPhoto) {

	$uploadDir = __DIR__ . '/../../uploads/employees/';
	if (!is_dir($uploadDir)) {
		if (!@mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
			return false;
		}
	}

	$newFileName = 'emp_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;

	if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
		return false;
	}

	/* Replaced photo: remove the file the record used to point at. */
	if ($oldPhoto != '' && strpos($oldPhoto, 'uploads/employees/') === 0) {
		$oldFull = __DIR__ . '/../../' . $oldPhoto;
		if (is_file($oldFull)) { @unlink($oldFull); }
	}

	return 'uploads/employees/' . $newFileName;
}


/* ---------------------------------------------------------------------
   Add (EMP_ID = 0) or update (EMP_ID > 0) an employee record.
   --------------------------------------------------------------------- */
function doSave() {

	global $mydb;

	$id     = isset($_POST['EMP_ID'])      ? intval($_POST['EMP_ID'])       : 0;
	$fname  = isset($_POST['E_FNAME'])     ? trim($_POST['E_FNAME'])        : '';
	$mname  = isset($_POST['E_MNAME'])     ? trim($_POST['E_MNAME'])        : '';
	$lname  = isset($_POST['E_LNAME'])     ? trim($_POST['E_LNAME'])        : '';
	$suffix = isset($_POST['E_SUFFIX'])    ? trim($_POST['E_SUFFIX'])       : '';
	$email  = isset($_POST['E_EMAIL'])     ? trim($_POST['E_EMAIL'])        : '';
	$phone  = isset($_POST['E_PHONE'])     ? trim($_POST['E_PHONE'])        : '';
	$dob    = isset($_POST['E_DOB'])       ? trim($_POST['E_DOB'])          : '';
	$hire   = isset($_POST['E_HIRE'])      ? trim($_POST['E_HIRE'])         : '';
	$job    = isset($_POST['E_JOBTITLE'])  ? trim($_POST['E_JOBTITLE'])     : '';
	$dept   = isset($_POST['E_DEPARTMENT'])? trim($_POST['E_DEPARTMENT'])   : '';
	$salary = isset($_POST['E_SALARY']) && $_POST['E_SALARY'] !== '' ? (float)$_POST['E_SALARY'] : null;
	$mgr    = isset($_POST['E_MANAGER'])   ? intval($_POST['E_MANAGER'])    : 0;
	$active = (isset($_POST['E_ISACTIVE']) && $_POST['E_ISACTIVE'] == '1') ? 1 : 0;

	$oldPhoto  = isset($_POST['E_PHOTO_OLD']) ? trim($_POST['E_PHOTO_OLD']) : '';
	$photoPath = $oldPhoto;

	/* ------------------------------------------------------------------
	   Check the photo BEFORE saving anything, but do not move it yet.
	   The old order moved the file first and validated the text fields
	   afterwards, so a form rejected for a missing last name still left
	   an orphaned image sitting in uploads/employees/.
	   ------------------------------------------------------------------ */
	$photoExt = validate_employee_photo(isset($_FILES['E_PHOTO']) ? $_FILES['E_PHOTO'] : null);
	if ($photoExt === false) {
		message("The photo must be a JPG, PNG, GIF or WEBP image under 2MB.", "error");
		redirect('index.php');
		return;
	}

	if ($fname == '' || $lname == '' || $email == '' || $hire == '') {
		message("First name, last name, email and hire date are required.", "error");
		redirect('index.php');
		return;
	}

	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		message("Please enter a valid email address.", "error");
		redirect('index.php');
		return;
	}

	/* Email is unique. Check here for a readable message rather than a raw
	   duplicate-key error, excluding this same record when editing. */
	$mydb->setQuery("SELECT EmployeeID FROM `tblemployee`
		WHERE Email = '".$mydb->escape_value($email)."'
		  AND EmployeeID <> '".$id."' LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("That email is already used by another employee.", "error");
		redirect('index.php');
		return;
	}

	/* A record cannot be its own manager. */
	if ($id > 0 && $mgr === $id) {
		message("An employee cannot be their own manager.", "error");
		redirect('index.php');
		return;
	}

	/* Everything checks out, so the file can be committed to disk now. */
	if ($photoExt !== '') {
		$stored = store_employee_photo($_FILES['E_PHOTO'], $photoExt, $oldPhoto);
		if ($stored === false) {
			message("The photo could not be saved. Check that the uploads/employees folder is writable.", "error");
			redirect('index.php');
			return;
		}
		$photoPath = $stored;
	}

	/* ------------------------------------------------------------------
	   NULL vs empty string.

	   Only DateOfBirth, ManagerID, Suffix and PhotoPath are nullable in
	   tblemployee. MiddleName, Phone, JobTitle, Department and Salary are
	   all NOT NULL, so writing NULL into them raised
	   "Integrity constraint violation: 1048 Column ... cannot be null",
	   which surfaced as a blank error page on submit. Those five now send
	   an empty string (0 for Salary) instead.
	   ------------------------------------------------------------------ */
	$dobSql    = ($dob == '') ? "NULL" : "'".$mydb->escape_value($dob)."'";        // nullable
	$mgrSql    = ($mgr > 0)   ? "'".$mgr."'" : "NULL";                             // nullable
	$suffixSql = ($suffix == '') ? "NULL" : "'".$mydb->escape_value($suffix)."'";  // nullable

	$salarySql = ($salary === null) ? "'0'" : "'".$salary."'";                     // NOT NULL
	$phoneSql  = "'".$mydb->escape_value($phone)."'";                              // NOT NULL
	$jobSql    = "'".$mydb->escape_value($job)."'";                                // NOT NULL
	$deptSql   = "'".$mydb->escape_value($dept)."'";                               // NOT NULL
	$mnameSql  = "'".$mydb->escape_value($mname)."'";                              // NOT NULL

	if ($id > 0) {
		$sql = "UPDATE `tblemployee` SET
				`FirstName`   = '".$mydb->escape_value($fname)."',
				`MiddleName`  = ".$mnameSql.",
				`LastName`    = '".$mydb->escape_value($lname)."',
				`Suffix`      = ".$suffixSql.",
				`Email`       = '".$mydb->escape_value($email)."',
				`Phone`       = ".$phoneSql.",
				`DateOfBirth` = ".$dobSql.",
				`HireDate`    = '".$mydb->escape_value($hire)."',
				`JobTitle`    = ".$jobSql.",
				`Department`  = ".$deptSql.",
				`Salary`      = ".$salarySql.",
				`ManagerID`   = ".$mgrSql.",
				`IsActive`    = '".$active."',
				`PhotoPath`   = '".$mydb->escape_value($photoPath)."'
			WHERE `EmployeeID` = '".$id."'";
		$ok = $mydb->InsertThis($sql);
		message($ok ? "Employee record updated." : "The employee could not be updated. ".$mydb->lastError(), $ok ? "success" : "error");
	} else {
		$sql = "INSERT INTO `tblemployee`
			(`FirstName`, `MiddleName`, `LastName`, `Suffix`, `Email`, `Phone`, `DateOfBirth`, `HireDate`,
			 `JobTitle`, `Department`, `Salary`, `ManagerID`, `IsActive`, `PhotoPath`)
			VALUES ('".$mydb->escape_value($fname)."', ".$mnameSql.", '".$mydb->escape_value($lname)."', ".$suffixSql.",
				'".$mydb->escape_value($email)."', ".$phoneSql.", ".$dobSql.",
				'".$mydb->escape_value($hire)."', ".$jobSql.", ".$deptSql.", ".$salarySql.",
				".$mgrSql.", '".$active."', '".$mydb->escape_value($photoPath)."')";
		$ok = $mydb->InsertThis($sql);

		if ($ok) {
			$newId = $mydb->insert_id();
			addNotification(
				"New Employee Added",
				$fname . " " . $lname . " was added to the system.",
				"employee",
				"module/employee/index.php?view=view&id=" . $newId
			);
		}

		message($ok ? "Employee added." : "The employee could not be added. ".$mydb->lastError(), $ok ? "success" : "error");
	}
	redirect('index.php');
}


function doDelete() {

	global $mydb;

	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	if ($id <= 0) {
		message("No employee selected.", "error");
		redirect('index.php');
		return;
	}

	/* Delete the photo file too, so removed staff do not leave images
	   behind in uploads/employees/. */
	$mydb->setQuery("SELECT PhotoPath FROM `tblemployee` WHERE `EmployeeID` = '".$id."' LIMIT 1");
	$row = $mydb->loadSingleResult();
	if ($row && !empty($row->PhotoPath) && strpos($row->PhotoPath, 'uploads/employees/') === 0) {
		$oldFull = __DIR__ . '/../../' . $row->PhotoPath;
		if (is_file($oldFull)) { @unlink($oldFull); }
	}

	/* Anyone managed by this person keeps their record; their ManagerID is
	   cleared automatically by the ON DELETE SET NULL foreign key. */
	if ($mydb->InsertThis("DELETE FROM `tblemployee` WHERE `EmployeeID` = '".$id."'")) {
		message("Employee record deleted.", "info");
	} else {
		message("The employee could not be deleted. ".$mydb->lastError(), "error");
	}
	redirect('index.php');
}
?>