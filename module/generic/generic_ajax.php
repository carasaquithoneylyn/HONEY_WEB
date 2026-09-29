<?php
// ABADIES SOLUTIONS

require_once("../../include/initialize.php");
require_once("config.php");
global $mydb;

$table = isset($_GET['t']) ? $_GET['t'] : (isset($_POST['t']) ? $_POST['t'] : '');
$cfg   = generic_table_config($table);

if (!$cfg) {
	echo json_encode(array('error' => 'Unknown table'));
	exit;
}

$rec     = new GenericRecord($table);
$columns = $rec->columns();
$fields  = array();
foreach ($columns as $c) { $fields[] = $c->Field; }

/* small in-request cache so FK labels aren't re-queried per row */
function generic_fk_label_cache($field, $value) {
	global $GENERIC_FK_MAP, $mydb;
	static $cache = array();
	if ($value === null || $value === '') { return ''; }
	if (!isset($GENERIC_FK_MAP[$field])) { return $value; }
	$fk = $GENERIC_FK_MAP[$field];
	$cacheKey = $field.':'.$value;
	if (isset($cache[$cacheKey])) { return $cache[$cacheKey]; }
	if (!$mydb->tableExists($fk['table'])) { return $value; }
	$mydb->setQuery("SELECT ".$fk['label']." as fk_label FROM `".$fk['table']."` WHERE `".$fk['pk']."` = '".$mydb->escape_value($value)."' LIMIT 1");
	$row = $mydb->loadSingleResult();
	$label = ($row && isset($row->fk_label)) ? $row->fk_label : $value;
	$cache[$cacheKey] = $label;
	return $label;
}

if (isset($_POST['record_id'])) {
	// single record for the Edit modal
	$row = $rec->single($_POST['record_id']);
	$output = array();
	if ($row) {
		foreach ($fields as $f) {
			$output[$f] = isset($row->$f) ? $row->$f : '';
		}
	}
	echo json_encode($output);
	exit;
}

$query = "SELECT * FROM `".$table."`";

if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] !== '') {
	$term = $mydb->escape_value($_POST["search"]["value"]);
	$likeParts = array();
	foreach ($fields as $f) {
		$likeParts[] = "`".$f."` LIKE '%".$term."%'";
	}
	if (!empty($likeParts)) {
		$query .= " WHERE (".join(" OR ", $likeParts).")";
	}
}

if (isset($_POST["order"]) && isset($fields[intval($_POST['order']['0']['column']) - 1])) {
	$orderField = $fields[intval($_POST['order']['0']['column']) - 1];
	$orderDir   = ($_POST['order']['0']['dir'] == 'asc') ? 'ASC' : 'DESC';
	$query .= " ORDER BY `".$orderField."` ".$orderDir;
} else {
	$query .= " ORDER BY `".$rec->pk."` DESC";
}

$countQuery = "SELECT * FROM `".$table."`";
$mydb->setQuery($countQuery);
$totalRecords = $mydb->num_rows();

$filteredQuery = $query;
$mydb->setQuery($filteredQuery);
$filteredRecords = $mydb->num_rows();

if (isset($_POST["length"]) && $_POST["length"] != -1) {
	$query .= " LIMIT " . intval($_POST['start']) . ", " . intval($_POST['length']);
}

$mydb->setQuery($query);
$rows = $mydb->loadResultList();

$data = array();
$i = 1;
foreach ($rows as $row) {
	$sub_array = array();
	$sub_array[] = $i;
	foreach ($fields as $f) {
		$value = isset($row->$f) ? $row->$f : '';
		$sub_array[] = htmlspecialchars(generic_fk_label_cache($f, $value));
	}
	$pkVal = isset($row->{$rec->pk}) ? $row->{$rec->pk} : '';
	$sub_array[] = '
		<button type="button" class="btn btn-warning btn-xs editEntry" data-id="'.$pkVal.'"><span class="fa fa-edit fw-fa"></span></button>
		<button type="button" class="btn btn-danger btn-xs deleteEntry" data-id="'.$pkVal.'"><span class="fa fa-trash"></span></button>';
	$data[] = $sub_array;
	$i = $i + 1;
}

$output = array(
	'data'            => $data,
	'recordsTotal'    => $totalRecords,
	'recordsFiltered' => $filteredRecords
);
echo json_encode($output);
?>
