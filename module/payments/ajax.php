<?php
// Payments module data endpoints
require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* -----------------------------------------------------------------
   Assessment + running balance for one enrollment record. Used by the
   Add Payment modal's info panel.

   These figures are computed EXACTLY the same way module/cashier/soa.php
   builds the Statement of Account, so the Payments screen and the SOA
   always show the same Total Assessment and Balance for a student:

     Assessment = tuition (tblfeeschedule by COURSE_ID + YEAR_LEVEL,
                  falling back to any TUITION rows in tblfeeitems)
                + every STANDARD / NONSTANDARD / OTHER item in tblfeeitems
                  for that year level (this is Fee Setup)
     Paid       = sum of ALL payments recorded for the enrolment
     Balance    = Assessment - Paid
   ----------------------------------------------------------------- */
if ($act === 'balance') {

	$eid = intval($_POST['ENROLLMENT_ID']);
	$output = array(
		'COURSE_TEXT' => '',
		'YEAR_LEVEL'  => '',
		'ASSESSMENT'  => 0,
		'TOTAL_PAID'  => 0,
		'BALANCE'     => 0
	);

	$mydb->setQuery("SELECT e.COURSE_ID, e.YEAR_LEVEL, c.COURSE_CODE, c.COURSE_NAME
		FROM `tblenrollment` e
		JOIN `tblcourses` c ON c.COURSE_ID = e.COURSE_ID
		WHERE e.ENROLLMENT_ID = '".$eid."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) >= 1) {
		$rec = $rows[0];
		$yl  = $rec->YEAR_LEVEL;
		$output['COURSE_TEXT'] = $rec->COURSE_CODE.' - '.$rec->COURSE_NAME;
		$output['YEAR_LEVEL']  = $yl;

		/* Itemised fees (Fee Setup) for this year level. */
		$itemsTotal = 0.0;
		$tuitionFromItems = 0.0;
		if ($mydb->tableExists('tblfeeitems')) {
			$mydb->setQuery("SELECT CATEGORY, AMOUNT FROM `tblfeeitems`
				WHERE YEAR_LEVEL = '".$mydb->escape_value($yl)."'");
			foreach ($mydb->loadResultList() as $it) {
				if ($it->CATEGORY === 'TUITION') { $tuitionFromItems += (float)$it->AMOUNT; }
				else { $itemsTotal += (float)$it->AMOUNT; }
			}
		}

		/* Tuition is per COURSE + YEAR LEVEL, from the fee schedule (edited in
		   Fee Setup > Tuition Fee per Course) - same rule as soa.php. */
		$tuition = 0.0;
		if ($mydb->tableExists('tblfeeschedule')) {
			$mydb->setQuery("SELECT TUITION_FEE FROM `tblfeeschedule`
				WHERE COURSE_ID = '".intval($rec->COURSE_ID)."' AND YEAR_LEVEL = '".$mydb->escape_value($yl)."'
				LIMIT 1");
			$fee = $mydb->loadResultList();
			if (count($fee) >= 1) { $tuition = (float)$fee[0]->TUITION_FEE; }
		}
		if ($tuition <= 0) { $tuition = $tuitionFromItems; }

		$assessment = $tuition + $itemsTotal;
		$output['ASSESSMENT'] = $assessment;

		$mydb->setQuery("SELECT COALESCE(SUM(AMOUNT), 0) AS TOTAL FROM `tblpayments`
			WHERE ENROLLMENT_ID = '".$eid."'");
		$paidRows = $mydb->loadResultList();
		$paid = (count($paidRows) >= 1) ? (float)$paidRows[0]->TOTAL : 0;
		$output['TOTAL_PAID'] = $paid;

		$output['BALANCE'] = $assessment - $paid;
	}

	echo json_encode($output);
	exit;
}

/* -----------------------------------------------------------------
   DataTables list
   ----------------------------------------------------------------- */
$base = "FROM `tblpayments` p 
	JOIN `tblenrollment` e ON e.ENROLLMENT_ID = p.ENROLLMENT_ID 
	JOIN `tblstudent`    s ON s.S_ID          = e.S_ID 
	JOIN `tblcourses`    c ON c.COURSE_ID     = e.COURSE_ID 
	LEFT JOIN `tblusers` u ON u.UID           = p.RECEIVED_BY ";

$where = " WHERE 1=1 ";

$filter = isset($_POST['fee_type_filter']) ? trim($_POST['fee_type_filter']) : '';
if ($filter !== '') {
	$where .= " AND p.FEE_TYPE = '".$mydb->escape_value($filter)."' ";
}

if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
	$s = $mydb->escape_value($_POST["search"]["value"]);
	$where .= " AND (s.LNAME LIKE '%".$s."%' 
				 OR s.FNAME LIKE '%".$s."%' 
				 OR s.IDNO  LIKE '%".$s."%' 
				 OR p.OR_NUMBER LIKE '%".$s."%' 
				 OR p.FEE_TYPE LIKE '%".$s."%') ";
}

$orderCols = array(
	0 => 'p.PAYMENT_ID',
	1 => 'p.OR_NUMBER',
	2 => 's.LNAME',
	3 => 'c.COURSE_CODE',
	4 => 'p.FEE_TYPE',
	5 => 'p.AMOUNT',
	6 => 'p.DATE_PAID',
	7 => 'u.DISPLAYNAME'
);

$orderBy = " ORDER BY p.PAYMENT_ID DESC ";
if (isset($_POST['order'][0]['column'])) {
	$ci = intval($_POST['order'][0]['column']);
	$dir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) == 'asc') ? 'ASC' : 'DESC';
	if (isset($orderCols[$ci])) {
		$orderBy = " ORDER BY ".$orderCols[$ci]." ".$dir." ";
	}
}

$limit = "";
if (isset($_POST['length']) && $_POST['length'] != -1) {
	$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
}

$select = "SELECT p.PAYMENT_ID, p.OR_NUMBER, p.FEE_TYPE, p.AMOUNT, p.DATE_PAID, 
		s.IDNO, s.LNAME, s.FNAME, s.MNAME, 
		c.COURSE_CODE, u.DISPLAYNAME ";

$mydb->setQuery($select.$base.$where.$orderBy.$limit);
$rows = $mydb->loadResultList();

$mydb->setQuery("SELECT p.PAYMENT_ID ".$base.$where);
$filtered = $mydb->num_rows();

$mydb->setQuery("SELECT PAYMENT_ID FROM `tblpayments`");
$total = $mydb->num_rows();

$data = array();
$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

foreach ($rows as $r) {

	$feeClass = 'secondary';
	if ($r->FEE_TYPE == 'Enrollment Fee') { $feeClass = 'warning'; }
	if ($r->FEE_TYPE == 'Entrance Exam')  { $feeClass = 'info'; }
	if ($r->FEE_TYPE == 'Admission Fee')  { $feeClass = 'success'; }

	$receivedBy = ($r->DISPLAYNAME === null) ? '<span class="text-muted">-</span>' : htmlspecialchars($r->DISPLAYNAME);

	$actions = '
		<button type="button" PID="'.$r->PAYMENT_ID.'" class="btn btn-danger btn-xs deletePayment" title="Delete">
			<span class="fa fa-trash fw-fa"></span>
		</button>';

	$data[] = array(
		$i,
		htmlspecialchars($r->OR_NUMBER),
		htmlspecialchars($r->IDNO.' - '.trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME)),
		htmlspecialchars($r->COURSE_CODE),
		'<span class="badge badge-'.$feeClass.'">'.htmlspecialchars($r->FEE_TYPE).'</span>',
		'&#8369;'.number_format((float)$r->AMOUNT, 2),
		$r->DATE_PAID,
		$receivedBy,
		$actions
	);
	$i++;
}

echo json_encode(array(
	'data'            => $data,
	'recordsTotal'    => $total,
	'recordsFiltered' => $filtered
));
?>