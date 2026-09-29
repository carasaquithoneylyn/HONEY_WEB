<?php
/* =====================================================================
   ABADIES SOLUTIONS - notifications helper

   Call addNotification() anywhere after adding or changing a record and
   the bell in the top bar picks it up on its next poll (every 30s) and
   on the next page load.

   $mydb must already be available, which it is everywhere that has gone
   through include/initialize.php.
   ===================================================================== */

if (!function_exists('notifications_table_ready')) {

	/* Creates tblnotifications the first time it is needed, so an older
	   database that was imported before this table existed does not make
	   the bell silently do nothing. Checked once per request. */
	function notifications_table_ready() {
		global $mydb;
		static $ready = null;
		if ($ready !== null) { return $ready; }
		if (!isset($mydb)) { $ready = false; return false; }

		if ($mydb->tableExists('tblnotifications')) { $ready = true; return true; }

		$ready = $mydb->InsertThis(
			"CREATE TABLE IF NOT EXISTS `tblnotifications` (
				`NotificationID` INT(11) NOT NULL AUTO_INCREMENT,
				`Title`          VARCHAR(150) NOT NULL,
				`Message`        VARCHAR(255) NOT NULL,
				`Type`           VARCHAR(30)  NOT NULL DEFAULT 'general',
				`LinkURL`        VARCHAR(255) DEFAULT NULL,
				`IsRead`         TINYINT(1)   NOT NULL DEFAULT 0,
				`DateCreated`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (`NotificationID`),
				KEY `IsRead` (`IsRead`),
				KEY `DateCreated` (`DateCreated`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
		);
		return $ready;
	}

	/* Turns a project-relative path such as
	       module/employee/index.php?view=view&id=17
	   into a root-relative one such as
	       /abadies-class/module/employee/index.php?view=view&id=17

	   The old code stored the bare relative form, so clicking a
	   notification from inside a module resolved against that module's
	   folder and produced a dead link like
	       /abadies-class/module/employee/module/employee/index.php  */
	function notification_link($path) {
		$path = trim((string)$path);
		if ($path === '') { return null; }

		// Already absolute or a full URL: leave it alone.
		if ($path[0] === '/' || preg_match('#^https?://#i', $path)) { return $path; }

		return rtrim(WEB_ROOT, '/').'/'.ltrim($path, '/');
	}
}

if (!function_exists('addNotification')) {

	function addNotification($title, $message, $type = 'general', $linkUrl = '') {
		global $mydb;

		if (!notifications_table_ready()) { return false; }

		$link = notification_link($linkUrl);

		/* Columns are VARCHAR(150) / VARCHAR(255); trim so a long message
		   cannot be truncated by MySQL in strict mode and fail the insert. */
		$title   = mb_substr((string)$title,   0, 150);
		$message = mb_substr((string)$message, 0, 255);
		$type    = mb_substr((string)$type,    0, 30);

		$sql = "INSERT INTO `tblnotifications` (`Title`, `Message`, `Type`, `LinkURL`, `IsRead`, `DateCreated`)
			VALUES (
				'".$mydb->escape_value($title)."',
				'".$mydb->escape_value($message)."',
				'".$mydb->escape_value($type)."',
				".($link !== null ? "'".$mydb->escape_value($link)."'" : "NULL").",
				0,
				NOW()
			)";

		return $mydb->InsertThis($sql);
	}
}
?>