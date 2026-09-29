<?php
// ABADIES SOLUTIONS
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
}

function doInsert(){
	$subject = new Subject();
	$SUBJECT_CODE = trim($_POST['SUBJECT_CODE']);
	$SUBJECT_NAME = trim($_POST['SUBJECT_NAME']);
	$UNITS        = $_POST['UNITS'];
	$COURSE_ID    = $_POST['COURSE_ID'];
	$YEAR_LEVEL   = $_POST['YEAR_LEVEL'];
	$SEMESTER     = $_POST['SEMESTER'];

	$res = $subject->find_all_subject($SUBJECT_CODE);

	if ($res >= 1) {
		message("Subject Code already exist!", "error");
		redirect('index.php');
	} else {
		$subject->SUBJECT_CODE = $SUBJECT_CODE;
		$subject->SUBJECT_NAME = $SUBJECT_NAME;
		$subject->UNITS        = $UNITS;
		$subject->COURSE_ID    = $COURSE_ID;
		$subject->YEAR_LEVEL   = $YEAR_LEVEL;
		$subject->SEMESTER     = $SEMESTER;

		$istrue = $subject->create();
		if ($istrue == true){
			message("New Subject [". $SUBJECT_CODE ."] has been created successfully!", "success");
			redirect('index.php');
		} else {
			message("No subject has been created successfully!", "error");
			redirect('index.php');
		}
	}
}

function doEdit(){
	if (isset($_POST['edit'])) {
		$subject = new Subject();
		$SUBJECT_ID   = $_POST['SUBJECT_ID'];
		$SUBJECT_CODE = trim($_POST['SUBJECT_CODE1']);
		$SUBJECT_NAME = trim($_POST['SUBJECT_NAME1']);
		$UNITS        = $_POST['UNITS1'];
		$COURSE_ID    = $_POST['COURSE_ID1'];
		$YEAR_LEVEL   = $_POST['YEAR_LEVEL1'];
		$SEMESTER     = $_POST['SEMESTER1'];

		$subject->SUBJECT_CODE = $SUBJECT_CODE;
		$subject->SUBJECT_NAME = $SUBJECT_NAME;
		$subject->UNITS        = $UNITS;
		$subject->COURSE_ID    = $COURSE_ID;
		$subject->YEAR_LEVEL   = $YEAR_LEVEL;
		$subject->SEMESTER     = $SEMESTER;

		$istrue = $subject->update($SUBJECT_ID);
		if ($istrue == true){
			message("Subject [". $SUBJECT_CODE ."] has been Updated successfully!", "success");
			redirect('index.php');
		} else {
			message("No subject has been updated successfully!", "error");
			redirect('index.php');
		}
	}
}

function doDelete(){
	$id = $_GET['id'];
	$subject = new Subject();
	$subject->delete($id);

	message("Subject already Deleted!", "success");
	redirect('index.php');
}
?>
