<?php
/* =====================================================================
   ABADIES SOLUTIONS - notifications endpoint

   Returns the unread count plus the most recent notifications as JSON,
   and marks them read. Always replies with valid JSON, even when
   something is missing, so the bell never fails silently.
   ===================================================================== */

require_once(__DIR__ . "/../include/initialize.php");
global $mydb;

header('Content-Type: application/json');

/* Signed-in staff only. Without this the whole notification feed is
   readable by anyone who knows the URL. */
if (!isset($_SESSION['UID'])) {
	echo json_encode(array('unreadCount' => 0, 'notifications' => array(), 'auth' => false));
	exit;
}

$act = isset($_POST['act']) ? $_POST['act'] : (isset($_GET['act']) ? $_GET['act'] : 'list');

/* The table is created on demand by include/notifications.php, so an
   older database still works. If that fails, reply with an empty feed
   rather than a PHP error page that breaks the JSON. */
if (!notifications_table_ready()) {
	echo json_encode(array('unreadCount' => 0, 'notifications' => array(), 'ready' => false));
	exit;
}

/* -----------------------------------------------------------------
   Mark one, or all, as read.
   ----------------------------------------------------------------- */
if ($act === 'mark_read') {
	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	if ($id > 0) {
		$mydb->InsertThis("UPDATE `tblnotifications` SET `IsRead` = 1 WHERE `NotificationID` = '".$id."'");
	}
	echo json_encode(array('ok' => true));
	exit;
}

if ($act === 'mark_all_read') {
	$mydb->InsertThis("UPDATE `tblnotifications` SET `IsRead` = 1 WHERE `IsRead` = 0");
	echo json_encode(array('ok' => true));
	exit;
}

/* -----------------------------------------------------------------
   Default: recent notifications + unread count.
   ----------------------------------------------------------------- */
$mydb->setQuery("SELECT NotificationID, Title, Message, Type, LinkURL, IsRead, DateCreated
	FROM `tblnotifications`
	ORDER BY DateCreated DESC, NotificationID DESC
	LIMIT 12");

$list = array();
foreach ($mydb->loadResultList() as $r) {

	/* Rows written before links were stored root-relative hold a bare
	   path like "module/employee/index.php?view=view&id=17", which is
	   resolved against whatever folder the user is currently in and
	   produces a dead link. Normalise those on the way out so old rows
	   work without needing the database touched. */
	$link = trim((string)$r->LinkURL);
	if ($link !== '' && $link[0] !== '/' && !preg_match('#^https?://#i', $link)) {
		$link = rtrim(WEB_ROOT, '/').'/'.ltrim($link, '/');
	}

	$list[] = array(
		'id'      => (int)$r->NotificationID,
		'title'   => $r->Title,
		'message' => $r->Message,
		'type'    => $r->Type,
		'link'    => ($link === '' ? null : $link),
		'isRead'  => (int)$r->IsRead,
		'created' => $r->DateCreated
	);
}

$mydb->setQuery("SELECT COUNT(*) AS c FROM `tblnotifications` WHERE `IsRead` = 0");
$row = $mydb->loadSingleResult();
$unreadCount = $row ? (int)$row->c : 0;

echo json_encode(array(
	'unreadCount'   => $unreadCount,
	'notifications' => $list,
	'ready'         => true
));
?>