<?php
// Enrollment controller
require_once("../../include/initialize.php");
global $mydb;

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'section' :
		doSectioning();
		break;

	case 'edit' :
		doEdit();
		break;

	case 'delete' :
		doDelete();
		break;
}


/* ---------------------------------------------------------------------
   Confirms that a section really belongs to the given course and school
   year. Both modals let the user pick a section, and both are only as
   trustworthy as the browser sending them, so this is checked here.
   --------------------------------------------------------------------- */
function sectionBelongsTo($sectionId, $courseId, $syId) {
	global $mydb;
	$mydb->setQuery("SELECT SECTION_ID FROM `tblsections` 
		WHERE SECTION_ID = '".intval($sectionId)."' 
		  AND COURSE_ID  = '".intval($courseId)."' 
		  AND SY_ID      = '".intval($syId)."' 
		LIMIT 1");
	return ($mydb->num_rows() >= 1);
}


/* ---------------------------------------------------------------------
   STAGE 2: SECTIONING
   Assigns the section, stamps the enrollment date, and moves the record
   from Reserved to Enrolled.
   --------------------------------------------------------------------- */
function doSectioning() {

	global $mydb;

	$EID     = isset($_POST['SEC_EID'])     ? intval($_POST['SEC_EID'])     : 0;
	$SECTION = isset($_POST['SEC_SECTION']) ? intval($_POST['SEC_SECTION']) : 0;
	$DATE    = isset($_POST['SEC_DATE'])    ? trim($_POST['SEC_DATE'])      : '';
	$STATUS  = isset($_POST['SEC_STATUS'])  ? trim($_POST['SEC_STATUS'])    : 'Enrolled'; // default target status when opening Sectioning

	if ($EID <= 0 || $SECTION <= 0) {
		message("Please choose a section.", "error");
		redirect('index.php');
		return;
	}

	/* Read the course and school year from the RECORD, not from the form.
	   The hidden fields in the modal are only there to build the dropdown. */
	$mydb->setQuery("SELECT COURSE_ID, SY_ID, YEAR_LEVEL FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}
	$rec = $rows[0];

	if (!sectionBelongsTo($SECTION, $rec->COURSE_ID, $rec->SY_ID)) {
		message("That section does not belong to this student's course and academic year.", "error");
		redirect('index.php');
		return;
	}

	/* Sectioning is the last step of the enrollment process - it should
	   not happen until the Registrar has collected the Admission Fee.
	   Checked here rather than trusted from the form, since this is the
	   one hard requirement in the whole flow. */
	$mydb->setQuery("SELECT PAYMENT_ID FROM `tblpayments` 
		WHERE ENROLLMENT_ID = '".$EID."' AND FEE_TYPE = 'Admission Fee' 
		LIMIT 1");
	if ($mydb->num_rows() < 1) {
		message("This student still needs to pay the Admission Fee at Payments before sectioning.", "error");
		redirect('index.php');
		return;
	}

	if ($DATE == '') { $DATE = date('Y-m-d'); }

	/* A Reserved or Dropped record has no enrollment date. Only stamp one
	   when the record is actually being enrolled or completed. */
	$dateSql = ($STATUS == 'Enrolled' || $STATUS == 'Completed')
		? "'".$mydb->escape_value($DATE)."'"
		: "NULL";

	$sql = "UPDATE `tblenrollment` SET 
			`SECTION_ID`    = '".$SECTION."', 
			`DATE_ENROLLED` = ".$dateSql.", 
			`STATUS`        = '".$mydb->escape_value($STATUS)."' 
		WHERE `ENROLLMENT_ID` = '".$EID."'";

	if ($mydb->InsertThis($sql)) {
		message("Sectioning saved. The student is now ".$STATUS.".", "success");
	} else {
		message("Sectioning could not be saved.", "error");
	}
	redirect('index.php');
}


/* ---------------------------------------------------------------------
   EDIT an existing enrollment record
   --------------------------------------------------------------------- */
function doEdit() {

	global $mydb;

	$EID        = isset($_POST['E_EID'])            ? intval($_POST['E_EID'])           : 0;
	$SY_ID      = isset($_POST['E_SY'])             ? intval($_POST['E_SY'])            : 0;
	$COURSE_ID  = isset($_POST['E_COURSE'])         ? intval($_POST['E_COURSE'])        : 0;
	$SECTION_ID = isset($_POST['E_SECTION'])        ? intval($_POST['E_SECTION'])       : 0;
	$YEAR_LEVEL = isset($_POST['E_YEARLEVEL'])      ? trim($_POST['E_YEARLEVEL'])       : '';
	$SEMESTER   = isset($_POST['E_SEMESTER'])       ? trim($_POST['E_SEMESTER'])        : '';
	$CATEGORY   = isset($_POST['E_CATEGORY'])       ? trim($_POST['E_CATEGORY'])        : 'New';
	$CURRICULUM = isset($_POST['E_CURRICULUM'])     ? trim($_POST['E_CURRICULUM'])      : '';
	$STATUS     = isset($_POST['E_STATUS'])         ? trim($_POST['E_STATUS'])          : 'Registered';
	$RESERVED   = isset($_POST['E_DATE_RESERVED'])  ? trim($_POST['E_DATE_RESERVED'])   : '';
	$ENROLLED   = isset($_POST['E_DATE_ENROLLED'])  ? trim($_POST['E_DATE_ENROLLED'])   : '';

	if ($EID <= 0 || $SY_ID <= 0 || $COURSE_ID <= 0 || $YEAR_LEVEL == '' || $SEMESTER == '') {
		message("Please complete all the required fields.", "error");
		redirect('index.php');
		return;
	}

	/* Section is optional here, because a record can legitimately still be
	   unsectioned. But if one IS given it has to be a valid pairing. */
	if ($SECTION_ID > 0 && !sectionBelongsTo($SECTION_ID, $COURSE_ID, $SY_ID)) {
		message("That section does not belong to the selected course and academic year.", "error");
		redirect('index.php');
		return;
	}

	/* The unique key is (S_ID, SY_ID, SEMESTER). Changing the term could
	   collide with another record for the same student, so check first and
	   give a readable message instead of a raw database error. */
	$mydb->setQuery("SELECT S_ID FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$EID."' LIMIT 1");
	$owner = $mydb->loadResultList();
	if (count($owner) < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}
	$S_ID = $owner[0]->S_ID;

	$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` 
		WHERE S_ID     = '".$S_ID."' 
		  AND SY_ID    = '".$SY_ID."' 
		  AND SEMESTER = '".$mydb->escape_value($SEMESTER)."' 
		  AND ENROLLMENT_ID <> '".$EID."' 
		LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("This student already has another record for that academic year and semester.", "error");
		redirect('index.php');
		return;
	}

	$sectionSql    = ($SECTION_ID > 0) ? "'".$SECTION_ID."'" : "NULL";
	$curriculumSql = ($CURRICULUM == '') ? "NULL" : "'".$mydb->escape_value($CURRICULUM)."'";
	$reservedSql   = ($RESERVED == '')   ? "NULL" : "'".$mydb->escape_value($RESERVED)."'";
	$enrolledSql   = ($ENROLLED == '')   ? "NULL" : "'".$mydb->escape_value($ENROLLED)."'";

	$sql = "UPDATE `tblenrollment` SET 
			`COURSE_ID`     = '".$COURSE_ID."', 
			`SECTION_ID`    = ".$sectionSql.", 
			`SY_ID`         = '".$SY_ID."', 
			`YEAR_LEVEL`    = '".$mydb->escape_value($YEAR_LEVEL)."', 
			`SEMESTER`      = '".$mydb->escape_value($SEMESTER)."', 
			`CATEGORY`      = '".$mydb->escape_value($CATEGORY)."', 
			`CURRICULUM_YR` = ".$curriculumSql.", 
			`DATE_RESERVED` = ".$reservedSql.", 
			`DATE_ENROLLED` = ".$enrolledSql.", 
			`STATUS`        = '".$mydb->escape_value($STATUS)."' 
		WHERE `ENROLLMENT_ID` = '".$EID."'";

	if ($mydb->InsertThis($sql)) {
		message("Enrollment record updated.", "success");
	} else {
		message("The enrollment record could not be updated.", "error");
	}
	redirect('index.php');
}


function doDelete() {

	global $mydb;

	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

	if ($id <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php');
		return;
	}

	/* tblenrollment_details and tblgrades both cascade from here, so the
	   user should know what else goes with it. */
	$mydb->setQuery("SELECT DETAIL_ID FROM `tblenrollment_details` WHERE ENROLLMENT_ID = '".$id."'");
	$subjects = $mydb->num_rows();
	$mydb->setQuery("SELECT GRADE_ID FROM `tblgrades` WHERE ENROLLMENT_ID = '".$id."'");
	$grades = $mydb->num_rows();

	if ($mydb->InsertThis("DELETE FROM `tblenrollment` WHERE `ENROLLMENT_ID` = '".$id."'")) {
		$extra = '';
		if ($subjects > 0 || $grades > 0) {
			$extra = " ".$subjects." subject record(s) and ".$grades." grade record(s) were removed with it.";
		}
		message("Enrollment record deleted.".$extra, "info");
	} else {
		message("The enrollment record could not be deleted.", "error");
	}
	redirect('index.php');
}
?>