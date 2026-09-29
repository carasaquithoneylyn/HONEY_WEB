<?php
require_once ("../../include/initialize.php");
	  if (!isset($_SESSION['ACCOUNT_ID'])){
     // redirect(web_root."admin/index.php");
     }

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'add' :
	doInsert();
	break;
	
	case 'edit' :
	doEdit();
	break;
	
	case 'delete' :
	doDelete();
	break;

	case 'register' :
	doRegister();
	break;
	 
	}
    
	/* ---------------------------------------------------------------------
	   STUDENT PHOTO UPLOAD

	   Split into two steps on purpose:

	   validate_student_photo() only INSPECTS the upload - checks its real
	   content (not the filename extension the browser sent), its size,
	   and returns which extension to save it with. It never touches disk.

	   store_student_photo() does the actual move_uploaded_file(). PHP only
	   allows that to happen once per upload, and the final filename is
	   named after the student's own S_ID - which does not exist until
	   AFTER the INSERT - so validation has to happen first and the move
	   has to happen after, or the file would already be gone by the time
	   the real S_ID is known.
	   --------------------------------------------------------------------- */
	function validate_student_photo($file) {
		if (!isset($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
			return ''; // nothing chosen - photo is optional, not an error
		}
		if ($file['error'] !== UPLOAD_ERR_OK) {
			return false;
		}
		if ($file['size'] > 3 * 1024 * 1024) { // 3MB cap
			return false;
		}
		// getimagesize() reads the actual file header, so a renamed .php
		// cannot pass itself off as a .jpg here.
		$info = @getimagesize($file['tmp_name']);
		if ($info === false) {
			return false;
		}
		$allowed = array('image/jpeg' => 'jpg', 'image/png' => 'png');
		return isset($allowed[$info['mime']]) ? $allowed[$info['mime']] : false;
	}

	function store_student_photo($file, $ext, $sid) {
		$dir = __DIR__.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR;

		// Clear out any older photo for this student under a different
		// extension, so replacing a .jpg with a .png does not leave both
		// files sitting in the folder.
		foreach (array('jpg', 'png') as $oldExt) {
			$old = $dir.'S_'.$sid.'.'.$oldExt;
			if (is_file($old)) { @unlink($old); }
		}

		$filename = 'S_'.$sid.'.'.$ext;
		if (!move_uploaded_file($file['tmp_name'], $dir.$filename)) {
			return false;
		}
		return $filename;
	}

	function doInsert(){

		global $mydb;

		$student = new Student();

		$IDNO   = $_POST['IDNO'];
		$FNAME	= $_POST['FNAME'];
		$LNAME 	= $_POST['LNAME'];
		$MNAME 		= $_POST['MNAME'];
		$SEX 		= $_POST['SEX'];
		$BDAY  = $_POST['BDAY'];

		// The rest of the fields shown on the student's View page.
		$BPLACE      = isset($_POST['BPLACE'])      ? $_POST['BPLACE']      : '';
		$AGE         = isset($_POST['AGE'])         ? $_POST['AGE']         : '';
		$NATIONALITY = isset($_POST['NATIONALITY']) ? $_POST['NATIONALITY'] : '';
		$RELIGION    = isset($_POST['RELIGION'])    ? $_POST['RELIGION']    : '';
		$CONTACT_NO  = isset($_POST['CONTACT_NO'])  ? $_POST['CONTACT_NO']  : '';
		$EMAIL       = isset($_POST['EMAIL'])       ? $_POST['EMAIL']       : '';
		$HOME_ADD    = isset($_POST['HOME_ADD'])    ? $_POST['HOME_ADD']    : '';
		

		$res = $student->find_all_student($IDNO);
		
		
			if ($res >=1) {
				message("Student IDNO already exist!", "error");
				redirect('index.php');
			}else{

				/* Validate the photo BEFORE creating the record. A bad
				   upload should stop here, not leave a half-finished
				   student in the database. */
				$photoFile = isset($_FILES['PHOTO']) ? $_FILES['PHOTO'] : null;
				$photoExt  = validate_student_photo($photoFile);
				if ($photoExt === false) {
					message("Photo must be a JPG or PNG file, up to 3MB.", "error");
					redirect('index.php');
					return;
				}
				
				$student->IDNO = $IDNO;
				$student->FNAME = $FNAME;
				$student->LNAME = $LNAME;
				$student->MNAME 	= $MNAME;
				$student->SEX 	= $SEX;
				$student->BDAY 	= $BDAY;
				$student->BPLACE      = $BPLACE;
				$student->AGE         = (int)$AGE;
				$student->NATIONALITY = $NATIONALITY;
				$student->RELIGION    = $RELIGION;
				$student->CONTACT_NO  = $CONTACT_NO;
				$student->EMAIL       = $EMAIL;
				$student->HOME_ADD    = $HOME_ADD;
				
				 
				 $istrue = $student->create(); 
				 
				 		if ($istrue == true) {

					 		/* The photo is named after S_ID, which only exists now
					 		   that the INSERT has run, so this is a quick follow-up
					 		   UPDATE rather than part of the INSERT itself. */
					 		if ($photoExt !== '') {
					 			$newId = $mydb->insert_id();
					 			$savedName = store_student_photo($photoFile, $photoExt, $newId);
					 			if ($savedName !== false) {
					 				$mydb->InsertThis("UPDATE `tblstudent` SET `PHOTO` = '".$mydb->escape_value($savedName)."' WHERE `S_ID` = '".$newId."'");
					 			}
					 		}

					 		message("New Student [". $IDNO ."] has been created successfully!", "success");
					 		redirect('index.php');
					 	}else{
					 		message("No user has been created successfully!", "error");
					 		redirect('index.php');
					 	}
			}	 

	}
	function doEdit(){

			$student = new Student();

			$UID   = $_POST['UID'];

			//`IDNO`, `FNAME`, `LNAME`, `MNAME`, `SEX`, `BDAY``

			$IDNO   = $_POST['IDNO1'];
			$FNAME	= $_POST['FNAME1'];
			$MNAME	= $_POST['MNAME1'];
			$LNAME	= $_POST['LNAME1'];
			$SEX	= isset($_POST['SEX1'])  ? $_POST['SEX1']  : '';
			$BDAY	= isset($_POST['BDAY1']) ? $_POST['BDAY1'] : '';

			$BPLACE      = isset($_POST['BPLACE1'])      ? trim($_POST['BPLACE1'])      : '';
			$AGE         = isset($_POST['AGE1'])         ? trim($_POST['AGE1'])         : '';
			$NATIONALITY = isset($_POST['NATIONALITY1']) ? trim($_POST['NATIONALITY1']) : '';
			$RELIGION    = isset($_POST['RELIGION1'])    ? trim($_POST['RELIGION1'])    : '';
			$CONTACT_NO  = isset($_POST['CONTACT_NO1'])  ? trim($_POST['CONTACT_NO1'])  : '';
			$EMAIL       = isset($_POST['EMAIL1'])       ? trim($_POST['EMAIL1'])       : '';
			$HOME_ADD    = isset($_POST['HOME_ADD1'])    ? trim($_POST['HOME_ADD1'])    : '';
					
					
				$student->IDNO = $IDNO;
				$student->FNAME = $FNAME;
				$student->MNAME = $MNAME;
				$student->LNAME = $LNAME;

				/* Only overwrite gender when a real choice was made, so a blank
				   dropdown never wipes an existing value. */
				if ($SEX == 'Male' || $SEX == 'Female') {
					$student->SEX = $SEX;
				}

				/* Same guard for the date: an empty date input must not turn a
				   stored BDAY into 0000-00-00. */
				if ($BDAY != '') {
					$student->BDAY = $BDAY;
				}

				/* Same idea for everything else on this form - a field left
				   blank on submit never overwrites data that already exists. */
				if ($BPLACE !== '')      { $student->BPLACE = $BPLACE; }
				if ($AGE !== '')         { $student->AGE = (int)$AGE; }
				if ($NATIONALITY !== '') { $student->NATIONALITY = $NATIONALITY; }
				if ($RELIGION !== '')    { $student->RELIGION = $RELIGION; }
				if ($CONTACT_NO !== '')  { $student->CONTACT_NO = $CONTACT_NO; }
				if ($EMAIL !== '')       { $student->EMAIL = $EMAIL; }
				if ($HOME_ADD !== '')    { $student->HOME_ADD = $HOME_ADD; }
				
				 
				 $istrue = $student->update($UID); 
				 if ($istrue == true){
				 	
				 	message("Details has been Updated successfully!", "success");
				 	redirect('index.php');
				 	
				 }else{
				 	message("No user account has been updated successfully!", "error");
				 	redirect('index.php');
				 }
		
	
	}


	/* -------------------------------------------------------------------
	   STAGE 1 of the enrollment flow: REGISTER

	   Creates the tblenrollment row with STATUS = Registered. SECTION_ID
	   and DATE_ENROLLED are deliberately left NULL - they get filled in
	   during Stage 2 (Sectioning) on the Enrollment screen.

	   Ends by sending the user to Enrollment so the new registration is
	   right in front of them, ready to be sectioned.
	   ------------------------------------------------------------------- */
	function doRegister(){

		global $mydb;

		$S_ID       = isset($_POST['R_SID'])           ? intval($_POST['R_SID'])          : 0;
		$SY_ID      = isset($_POST['R_SY'])            ? intval($_POST['R_SY'])           : 0;
		$COURSE_ID  = isset($_POST['R_COURSE'])        ? intval($_POST['R_COURSE'])       : 0;
		$YEAR_LEVEL = isset($_POST['R_YEARLEVEL'])     ? trim($_POST['R_YEARLEVEL'])      : '';
		$SEMESTER   = isset($_POST['R_SEMESTER'])      ? trim($_POST['R_SEMESTER'])       : '';
		$CATEGORY   = isset($_POST['R_CATEGORY'])      ? trim($_POST['R_CATEGORY'])       : 'New';
		$CURRICULUM = isset($_POST['R_CURRICULUM'])    ? trim($_POST['R_CURRICULUM'])     : '';
		$RESERVED   = isset($_POST['R_DATE_RESERVED']) ? trim($_POST['R_DATE_RESERVED'])  : '';

		$enrollmentPage = WEB_ROOT.'module/enrollment/index.php';

		if ($S_ID <= 0 || $SY_ID <= 0 || $COURSE_ID <= 0 || $YEAR_LEVEL == '' || $SEMESTER == '') {
			message("Please complete all the reservation fields.", "error");
			redirect('index.php');
			return;
		}

		if ($RESERVED == '') { $RESERVED = date('Y-m-d'); }

		/* One registration per student per school year per semester. The
		   database also enforces this with a unique key, but catching it
		   here gives a readable message instead of a PDO error. */
		$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` 
			WHERE S_ID     = '".$S_ID."' 
			  AND SY_ID    = '".$SY_ID."' 
			  AND SEMESTER = '".$mydb->escape_value($SEMESTER)."' 
			LIMIT 1");
		if ($mydb->num_rows() >= 1) {
			message("This student already has a record for that academic year and semester.", "error");
			redirect('index.php');
			return;
		}

		$encodedBy = isset($_SESSION['UID']) ? intval($_SESSION['UID']) : 0;
		$encodedSql = ($encodedBy > 0) ? "'".$encodedBy."'" : "NULL";

		$curriculumSql = ($CURRICULUM == '') ? "NULL" : "'".$mydb->escape_value($CURRICULUM)."'";

		$sql = "INSERT INTO `tblenrollment` 
			(`S_ID`, `COURSE_ID`, `SECTION_ID`, `SY_ID`, `YEAR_LEVEL`, `SEMESTER`, 
			 `CATEGORY`, `CURRICULUM_YR`, `DATE_RESERVED`, `DATE_ENROLLED`, `STATUS`, `ENCODED_BY`) 
			VALUES ('".$S_ID."', '".$COURSE_ID."', NULL, '".$SY_ID."', 
			'".$mydb->escape_value($YEAR_LEVEL)."', '".$mydb->escape_value($SEMESTER)."', 
			'".$mydb->escape_value($CATEGORY)."', ".$curriculumSql.", 
			'".$mydb->escape_value($RESERVED)."', NULL, 'Registered', ".$encodedSql.")";

		$istrue = $mydb->InsertThis($sql);

		if ($istrue) {

			/* Keep the student record in step with the course they registered
			   for, so the Student list shows the right program. */
			$mydb->InsertThis("UPDATE `tblstudent` SET `COURSE_ID` = '".$COURSE_ID."' WHERE `S_ID` = '".$S_ID."'");

			message("Slot registered. Use Enrollment to complete the sectioning.", "success");
			redirect($enrollmentPage);
		} else {
			message("The registration could not be saved.", "error");
			redirect('index.php');
		}
	}


	


	function doDelete(){
		
				$id = 	$_GET['id'];

				$student = New Student();
	 		 	$student->delete($id);
			 
			message("Student already Deleted!","info");
			redirect('index.php');
		
	}

	
?>