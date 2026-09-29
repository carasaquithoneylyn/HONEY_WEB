<?php
// Payments controller
require_once("../../include/initialize.php");
global $mydb;

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'add' :
		doAdd();
		break;

	case 'delete' :
		doDelete();
		break;

	case 'savefees' :
		doSaveFees();
		break;

	case 'addfee' :
		doAddFee();
		break;

	case 'deletefee' :
		doDeleteFee();
		break;

	case 'savetuition' :
		doSaveTuition();
		break;
}


/* ---------------------------------------------------------------------
   FEE SETUP (tblfeeitems) - lives on the same Cashier screen as Payments.
   These amounts drive the Statement of Account and the Add Payment balance.
   --------------------------------------------------------------------- */
function feesRedirect($fy) {
	redirect('index.php?tab=fees&feeyear='.urlencode($fy));
}

/* Save the edited amounts for a year level. */
function doSaveFees() {
	global $mydb;
	$fy   = isset($_POST['feeyear']) ? trim($_POST['feeyear']) : '1st Year';
	$amts = (isset($_POST['AMT']) && is_array($_POST['AMT'])) ? $_POST['AMT'] : array();

	$n = 0;
	foreach ($amts as $id => $val) {
		$id  = intval($id);
		$val = (float)$val;
		if ($id <= 0)  { continue; }
		if ($val < 0)  { $val = 0; }
		if ($mydb->InsertThis("UPDATE `tblfeeitems` SET `AMOUNT` = '".$val."' WHERE `FEEITEM_ID` = '".$id."'")) { $n++; }
	}
	message($n." fee item(s) updated for ".$fy.".", "success");
	feesRedirect($fy);
}

/* Add a new fee item. */
function doAddFee() {
	global $mydb;
	$fy   = isset($_POST['feeyear'])     ? trim($_POST['feeyear'])     : '1st Year';
	$cat  = isset($_POST['F_CATEGORY'])  ? trim($_POST['F_CATEGORY'])  : '';
	$name = isset($_POST['F_ITEM_NAME']) ? trim($_POST['F_ITEM_NAME']) : '';
	$amt  = isset($_POST['F_AMOUNT'])    ? (float)$_POST['F_AMOUNT']   : 0;

	$validCats  = array('TUITION', 'STANDARD', 'NONSTANDARD', 'OTHER');
	$validYears = array('1st Year', '2nd Year', '3rd Year', '4th Year');

	if (!in_array($cat, $validCats) || !in_array($fy, $validYears) || $name == '') {
		message("Please complete the fee item (category, name, amount).", "error");
		feesRedirect($fy);
		return;
	}
	if ($amt < 0) { $amt = 0; }

	/* The table has a unique key (CATEGORY, ITEM_NAME, YEAR_LEVEL); check
	   first so a duplicate gives a readable message, not a PDO error. */
	$mydb->setQuery("SELECT FEEITEM_ID FROM `tblfeeitems`
		WHERE CATEGORY = '".$mydb->escape_value($cat)."'
		  AND ITEM_NAME = '".$mydb->escape_value($name)."'
		  AND YEAR_LEVEL = '".$mydb->escape_value($fy)."' LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("That fee item already exists for ".$fy.".", "error");
		feesRedirect($fy);
		return;
	}

	/* Put new rows at the end of their year level's list. */
	$mydb->setQuery("SELECT COALESCE(MAX(SORT_ORDER),0) AS MX FROM `tblfeeitems` WHERE YEAR_LEVEL = '".$mydb->escape_value($fy)."'");
	$mx = $mydb->loadResultList();
	$sort = (count($mx) >= 1) ? intval($mx[0]->MX) + 1 : 1;

	$sql = "INSERT INTO `tblfeeitems` (`CATEGORY`, `ITEM_NAME`, `AMOUNT`, `YEAR_LEVEL`, `SORT_ORDER`)
		VALUES ('".$mydb->escape_value($cat)."', '".$mydb->escape_value($name)."', '".$amt."',
		        '".$mydb->escape_value($fy)."', '".$sort."')";
	if ($mydb->InsertThis($sql)) {
		message("Fee item added.", "success");
	} else {
		message("The fee item could not be added.", "error");
	}
	feesRedirect($fy);
}

/* Save tuition per COURSE for a year level (writes tblfeeschedule). */
function doSaveTuition() {
	global $mydb;
	$fy  = isset($_POST['feeyear']) ? trim($_POST['feeyear']) : '1st Year';
	$tui = (isset($_POST['TUI']) && is_array($_POST['TUI'])) ? $_POST['TUI'] : array();

	$validYears = array('1st Year', '2nd Year', '3rd Year', '4th Year');
	if (!in_array($fy, $validYears)) { $fy = '1st Year'; }

	$n = 0;
	foreach ($tui as $cid => $val) {
		$cid = intval($cid);
		$val = (float)$val;
		if ($cid <= 0) { continue; }
		if ($val < 0) { $val = 0; }
		/* One tuition figure per (course, year level). The table has a unique
		   key on that pair, so upsert in one statement. */
		$sql = "INSERT INTO `tblfeeschedule` (`COURSE_ID`, `YEAR_LEVEL`, `TUITION_FEE`)
			VALUES ('".$cid."', '".$mydb->escape_value($fy)."', '".$val."')
			ON DUPLICATE KEY UPDATE `TUITION_FEE` = '".$val."'";
		if ($mydb->InsertThis($sql)) { $n++; }
	}
	message($n." tuition figure(s) saved for ".$fy.".", "success");
	feesRedirect($fy);
}

/* Delete one fee item. */
function doDeleteFee() {
	global $mydb;
	$id = isset($_GET['id'])      ? intval($_GET['id'])       : 0;
	$fy = isset($_GET['feeyear']) ? trim($_GET['feeyear'])    : '1st Year';
	if ($id <= 0) {
		message("No fee item selected.", "error");
		feesRedirect($fy);
		return;
	}
	if ($mydb->InsertThis("DELETE FROM `tblfeeitems` WHERE `FEEITEM_ID` = '".$id."'")) {
		message("Fee item deleted.", "info");
	} else {
		message("The fee item could not be deleted.", "error");
	}
	feesRedirect($fy);
}


/* ---------------------------------------------------------------------
   Server-side re-check of the fixed amounts and the 1st-Year-only rule
   for Entrance Exam. The form already enforces these, but a form field
   is only as trustworthy as the browser sending it.
   --------------------------------------------------------------------- */
function doAdd() {

	global $mydb;

	$ENROLLMENT_ID = isset($_POST['P_ENROLLMENT']) ? intval($_POST['P_ENROLLMENT']) : 0;
	$FEE_TYPE      = isset($_POST['P_FEE_TYPE'])   ? trim($_POST['P_FEE_TYPE'])     : '';
	$AMOUNT        = isset($_POST['P_AMOUNT'])      ? (float)$_POST['P_AMOUNT']      : 0;
	$OR_NUMBER     = isset($_POST['P_OR_NUMBER'])   ? trim($_POST['P_OR_NUMBER'])    : '';
	$DATE_PAID     = isset($_POST['P_DATE_PAID'])   ? trim($_POST['P_DATE_PAID'])    : '';
	$REMARKS       = isset($_POST['P_REMARKS'])     ? trim($_POST['P_REMARKS'])      : '';

	$validFeeTypes = array('Tuition', 'Enrollment Fee', 'Entrance Exam', 'Admission Fee');
	$fixedAmounts  = array('Entrance Exam' => 150.00, 'Admission Fee' => 30.00);

	if ($ENROLLMENT_ID <= 0 || !in_array($FEE_TYPE, $validFeeTypes) || $OR_NUMBER == '' || $DATE_PAID == '') {
		message("Please complete all the required payment fields.", "error");
		redirect('index.php');
		return;
	}

	/* Confirm the enrollment record actually exists, and get its year
	   level for the Entrance Exam eligibility check. */
	$mydb->setQuery("SELECT YEAR_LEVEL FROM `tblenrollment` WHERE ENROLLMENT_ID = '".$ENROLLMENT_ID."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		message("That enrollment record no longer exists.", "error");
		redirect('index.php');
		return;
	}
	$yearLevel = $rows[0]->YEAR_LEVEL;

	if ($FEE_TYPE == 'Entrance Exam' && $yearLevel != '1st Year') {
		message("Entrance Exam fee only applies to 1st Year students.", "error");
		redirect('index.php');
		return;
	}

	/* Entrance Exam and Admission Fee are fixed - the amount submitted is
	   overwritten here rather than trusted, in case the readonly field
	   was tampered with client-side. */
	if (isset($fixedAmounts[$FEE_TYPE])) {
		$AMOUNT = $fixedAmounts[$FEE_TYPE];
	}

	if ($AMOUNT <= 0) {
		message("Amount must be greater than zero.", "error");
		redirect('index.php');
		return;
	}

	$receivedBy = isset($_SESSION['UID']) ? intval($_SESSION['UID']) : null;
	$receivedBySql = ($receivedBy) ? "'".$receivedBy."'" : "NULL";
	$remarksSql = ($REMARKS == '') ? "NULL" : "'".$mydb->escape_value($REMARKS)."'";

	$sql = "INSERT INTO `tblpayments` 
			(`ENROLLMENT_ID`, `FEE_TYPE`, `AMOUNT`, `OR_NUMBER`, `DATE_PAID`, `RECEIVED_BY`, `REMARKS`) 
		VALUES 
			('".$ENROLLMENT_ID."', '".$mydb->escape_value($FEE_TYPE)."', '".$AMOUNT."', 
			 '".$mydb->escape_value($OR_NUMBER)."', '".$mydb->escape_value($DATE_PAID)."', 
			 ".$receivedBySql.", ".$remarksSql.")";

	if ($mydb->InsertThis($sql)) {
		message($FEE_TYPE." of \u20b1".number_format($AMOUNT, 2)." recorded successfully.", "success");
	} else {
		message("The payment could not be saved.", "error");
	}
	redirect('index.php');
}


function doDelete() {

	global $mydb;

	$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

	if ($id <= 0) {
		message("No payment record selected.", "error");
		redirect('index.php');
		return;
	}

	if ($mydb->InsertThis("DELETE FROM `tblpayments` WHERE `PAYMENT_ID` = '".$id."'")) {
		message("Payment record deleted.", "info");
	} else {
		message("The payment record could not be deleted.", "error");
	}
	redirect('index.php');
}
?>