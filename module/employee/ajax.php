<?php
// Employee module data endpoints
require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* -----------------------------------------------------------------
   One employee row, for the Edit modal.
   ----------------------------------------------------------------- */
if ($act === 'row') {

	$id = intval($_POST['EmployeeID']);
	$output = array();

	$mydb->setQuery("SELECT * FROM `tblemployee` WHERE EmployeeID = '".$id."' LIMIT 1");
	foreach ($mydb->loadResultList() as $r) {
		$output['EmployeeID']  = $r->EmployeeID;
		$output['FirstName']   = $r->FirstName;
		$output['MiddleName']  = ($r->MiddleName === null) ? '' : $r->MiddleName;
		$output['LastName']    = $r->LastName;
		$output['Suffix']      = ($r->Suffix === null) ? '' : $r->Suffix;
		$output['Email']       = $r->Email;
		$output['Phone']       = ($r->Phone === null) ? '' : $r->Phone;
		$output['DateOfBirth'] = ($r->DateOfBirth === null || $r->DateOfBirth == '0000-00-00') ? '' : substr($r->DateOfBirth, 0, 10);
		$output['HireDate']    = ($r->HireDate === null || $r->HireDate == '0000-00-00') ? '' : substr($r->HireDate, 0, 10);
		$output['JobTitle']    = ($r->JobTitle === null) ? '' : $r->JobTitle;
		$output['Department']  = ($r->Department === null) ? '' : $r->Department;
		$output['Salary']      = ($r->Salary === null) ? '' : $r->Salary;
		$output['ManagerID']   = ($r->ManagerID === null) ? '' : $r->ManagerID;
		$output['IsActive']    = $r->IsActive;

		/* The edit form needs the existing photo back, otherwise saving
		   without picking a new file posts an empty E_PHOTO_OLD and the
		   controller stores an empty PhotoPath, wiping the photo. */
		$output['PhotoPath']   = ($r->PhotoPath === null) ? '' : $r->PhotoPath;
		$output['PhotoURL']    = ($r->PhotoPath === null || $r->PhotoPath === '')
			? ''
			: WEB_ROOT.$r->PhotoPath;
	}

	echo json_encode($output);
	exit;
}

/* -----------------------------------------------------------------
   DataTables list. A self-join resolves each employee's manager name.
   ----------------------------------------------------------------- */
$base = "FROM `tblemployee` e
	LEFT JOIN `tblemployee` m ON m.EmployeeID = e.ManagerID ";

$where = " WHERE 1=1 ";

/* Active / Inactive filter chips */
$filter = isset($_POST['status_filter']) ? trim($_POST['status_filter']) : '';
if ($filter === '1' || $filter === '0') {
	$where .= " AND e.IsActive = '".intval($filter)."' ";
}

if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
	$s = $mydb->escape_value($_POST["search"]["value"]);
	$where .= " AND (e.FirstName  LIKE '%".$s."%'
				 OR e.MiddleName LIKE '%".$s."%'
				 OR e.LastName   LIKE '%".$s."%'
				 OR e.Suffix     LIKE '%".$s."%'
				 OR e.Email      LIKE '%".$s."%'
				 OR e.JobTitle   LIKE '%".$s."%'
				 OR e.Department LIKE '%".$s."%') ";
}

$orderCols = array(
	0  => 'e.EmployeeID',
	1  => 'e.FirstName',
	2  => 'e.MiddleName',
	3  => 'e.LastName',
	4  => 'e.Suffix',
	5  => 'e.Email',
	6  => 'e.JobTitle',
	7  => 'e.Department',
	8  => 'm.LastName',
	9  => 'e.HireDate',
	10 => 'e.IsActive'
);

$orderBy = " ORDER BY e.LastName ASC, e.FirstName ASC ";
if (isset($_POST['order'][0]['column'])) {
	$ci  = intval($_POST['order'][0]['column']);
	$dir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) == 'asc') ? 'ASC' : 'DESC';
	if (isset($orderCols[$ci])) { $orderBy = " ORDER BY ".$orderCols[$ci]." ".$dir." "; }
}

$limit = "";
if (isset($_POST['length']) && $_POST['length'] != -1) {
	$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
}

$select = "SELECT e.EmployeeID, e.FirstName, e.MiddleName, e.LastName, e.Suffix,
		e.Email, e.JobTitle, e.Department, e.HireDate, e.IsActive,
		m.FirstName AS MgrFirst, m.LastName AS MgrLast ";

$mydb->setQuery($select.$base.$where.$orderBy.$limit);
$rows = $mydb->loadResultList();

$mydb->setQuery("SELECT e.EmployeeID ".$base.$where);
$filtered = $mydb->num_rows();

$mydb->setQuery("SELECT EmployeeID FROM `tblemployee`");
$total = $mydb->num_rows();

$data = array();

foreach ($rows as $r) {

	$statusBadge = ($r->IsActive == 1)
		? '<span class="badge badge-success">Active</span>'
		: '<span class="badge badge-secondary">Inactive</span>';

	$manager = ($r->MgrLast === null)
		? '<span class="text-muted">-</span>'
		: htmlspecialchars(trim($r->MgrLast.', '.$r->MgrFirst));

		$actions = '
		<a href="index.php?view=view&id='.$r->EmployeeID.'">
			<button type="button" class="btn btn-info btn-xs" title="View"><span class="fa fa-eye fw-fa"></span></button>
		</a>
		<button type="button" EID="'.$r->EmployeeID.'" class="btn btn-warning btn-xs editEmployee" title="Edit">
			<span class="fa fa-edit fw-fa"></span>
		</button>
		<a href="controller.php?action=delete&id='.$r->EmployeeID.'" onclick="return confirm(\'Delete this employee record?\');">
			<button type="button" class="btn btn-danger btn-xs" title="Delete"><span class="fa fa-trash fw-fa"></span></button>
		</a>';

	$data[] = array(
		htmlspecialchars($r->EmployeeID),
		htmlspecialchars($r->FirstName),
		($r->MiddleName === null || $r->MiddleName == '') ? '<span class="text-muted">-</span>' : htmlspecialchars($r->MiddleName),
		htmlspecialchars($r->LastName),
		($r->Suffix === null || $r->Suffix == '') ? '<span class="text-muted">-</span>' : htmlspecialchars($r->Suffix),
		htmlspecialchars($r->Email),
		($r->JobTitle === null || $r->JobTitle == '') ? '<span class="text-muted">-</span>' : htmlspecialchars($r->JobTitle),
		($r->Department === null || $r->Department == '') ? '<span class="text-muted">-</span>' : htmlspecialchars($r->Department),
		$manager,
		($r->HireDate === null) ? '<span class="text-muted">-</span>' : htmlspecialchars($r->HireDate),
		$statusBadge,
		$actions
	);
}

echo json_encode(array(
	'data'            => $data,
	'recordsTotal'    => $total,
	'recordsFiltered' => $filtered
));
?>