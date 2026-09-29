<?php
// ABADIES SOLUTIONS

require_once("../../include/initialize.php");
require_once("config.php");

$table = isset($_GET['t']) ? $_GET['t'] : '';
$cfg   = generic_table_config($table);

if (!$cfg) {
	message("Unknown table.", "error");
	redirect(WEB_ROOT."module/error/index.php?view=list");
	exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$backUrl = 'index.php?t='.urlencode($table);

switch ($action) {
	case 'add':
		doInsert($table, $cfg, $backUrl);
		break;
	case 'edit':
		doEdit($table, $cfg, $backUrl);
		break;
	case 'delete':
		doDelete($table, $cfg, $backUrl);
		break;
}

function doInsert($table, $cfg, $backUrl) {
	$rec = new GenericRecord($table);
	$data = $_POST;
	unset($data['save']);

	$ok = $rec->create($data);
	if ($ok) {
		message($cfg['title']." record has been added successfully!", "success");
	} else {
		message("No ".$cfg['title']." record has been created. Please check the values you entered.", "error");
	}
	redirect($backUrl);
}

function doEdit($table, $cfg, $backUrl) {
	$rec = new GenericRecord($table);
	$id  = isset($_POST['record_pk']) ? $_POST['record_pk'] : '';
	$data = $_POST;
	unset($data['edit'], $data['record_pk']);

	if ($id === '') {
		message("Could not determine which record to update.", "error");
		redirect($backUrl);
		return;
	}

	$ok = $rec->update($id, $data);
	if ($ok) {
		message($cfg['title']." record has been updated successfully!", "success");
	} else {
		message("No ".$cfg['title']." record has been updated.", "error");
	}
	redirect($backUrl);
}

function doDelete($table, $cfg, $backUrl) {
	$rec = new GenericRecord($table);
	$id  = isset($_GET['id']) ? $_GET['id'] : '';

	if ($id !== '') {
		$rec->delete($id);
	}
	message($cfg['title']." record has been deleted.", "success");
	redirect($backUrl);
}
?>
