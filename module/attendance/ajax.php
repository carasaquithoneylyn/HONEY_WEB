<?php
// Attendance module - RFID scan endpoint
require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* Only AM Time In has a lateness rule, matching the schedule given for
   this school: 8:00 AM with a 15 minute grace period. */
define('ATT_AM_TIME_IN_LATEST', '08:15:00');

/* Same fallback convention as theme/header.php's user avatar. */
function attendance_photo_url($photo) {
	$dir = dirname(__DIR__).DIRECTORY_SEPARATOR.'user'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
	$web = WEB_ROOT.'module/user/images/';

	if (!empty($photo)) {
		$safe = preg_replace('/[^A-Za-z0-9_.-]/', '', (string)$photo);
		if ($safe !== '' && is_file($dir.$safe)) {
			return $web.$safe.'?v='.filemtime($dir.$safe);
		}
	}
	return is_file($dir.'default.png') ? $web.'default.png' : '';
}

if ($act === 'scan') {

	$rfid   = isset($_POST['RFID_NUMBER']) ? trim($_POST['RFID_NUMBER']) : '';
	$action = isset($_POST['ACTION']) ? trim($_POST['ACTION']) : '';

	$validActions = array('AM_TIME_IN', 'AM_TIME_OUT', 'PM_TIME_IN', 'PM_TIME_OUT');

	if ($rfid === '' || !in_array($action, $validActions)) {
		echo json_encode(array('success' => false, 'message' => 'Missing card number or action.'));
		exit;
	}

	$mydb->setQuery("SELECT UID, DISPLAYNAME, USERNAME, TYPE, PHOTO FROM `tblusers` 
		WHERE RFID_NUMBER = '".$mydb->escape_value($rfid)."' AND STATUSACTIVE = 1 
		LIMIT 1");
	$userRows = $mydb->loadResultList();

	if (count($userRows) < 1) {
		/* Access denied: card not recognized (or belongs to a deactivated
		   account). This is the ONLY reason a scan is ever denied - once
		   the card is recognized, every tap after that is "granted",
		   even if that particular slot happens to be filled already. */
		echo json_encode(array('success' => false, 'message' => 'Unrecognized or inactive RFID card. Access denied.'));
		exit;
	}

	$user  = $userRows[0];
	$today = date('Y-m-d');
	$now   = date('H:i:s');
	$isPM  = ($now >= '12:00:00');

	/* Auto-correct the selected action if it disagrees with the actual
	   time of day - e.g. tapping "AM Time In" at 7 PM records PM Time In
	   instead, so a mis-tap can never land in the wrong half of the day. */
	$correctionNote = '';
	if (strpos($action, 'AM_') === 0 && $isPM) {
		$action = str_replace('AM_', 'PM_', $action);
		$correctionNote = ' (auto-adjusted to PM - it is already past noon)';
	} elseif (strpos($action, 'PM_') === 0 && !$isPM) {
		$action = str_replace('PM_', 'AM_', $action);
		$correctionNote = ' (auto-adjusted to AM - it is not noon yet)';
	}

	/* One row per staff per day. Create it the first time they scan today. */
	$mydb->setQuery("SELECT AM_TIME_IN, AM_TIME_OUT, PM_TIME_IN, PM_TIME_OUT 
		FROM `tblattendance` 
		WHERE UID = '".$user->UID."' AND ATTENDANCE_DATE = '".$today."' 
		LIMIT 1");
	$attRows = $mydb->loadResultList();

	if (count($attRows) < 1) {
		$mydb->InsertThis("INSERT INTO `tblattendance` (`UID`, `ATTENDANCE_DATE`) VALUES ('".$user->UID."', '".$today."')");
		$att = (object) array('AM_TIME_IN' => null, 'AM_TIME_OUT' => null, 'PM_TIME_IN' => null, 'PM_TIME_OUT' => null);
	} else {
		$att = $attRows[0];
	}

	$alreadySet = ($att->$action !== null);

	$message = '';
	if ($action == 'AM_TIME_IN') {
		$message = ($now > ATT_AM_TIME_IN_LATEST) ? 'Marked Late.' : 'On time.';
	} elseif ($action == 'AM_TIME_OUT') {
		$message = 'Enjoy your lunch.';
	} elseif ($action == 'PM_TIME_IN') {
		$message = 'Welcome back.';
	} else {
		$message = 'Have a good rest of your day.';
	}
	$message .= $correctionNote;

	if ($alreadySet) {
		$message = 'Already recorded earlier today.'.$correctionNote;
	} else {
		$mydb->InsertThis("UPDATE `tblattendance` SET `".$action."` = '".$now."' 
			WHERE UID = '".$user->UID."' AND ATTENDANCE_DATE = '".$today."'");
	}

	$actionLabels = array(
		'AM_TIME_IN'  => 'AM Time In',
		'AM_TIME_OUT' => 'AM Time Out',
		'PM_TIME_IN'  => 'PM Time In',
		'PM_TIME_OUT' => 'PM Time Out'
	);

	echo json_encode(array(
		'success'      => true,
		'name'         => $user->DISPLAYNAME,
		'username'     => $user->USERNAME,
		'role'         => $user->TYPE,
		'photo_url'    => attendance_photo_url($user->PHOTO),
		'action_label' => $actionLabels[$action],
		'time'         => date('g:i A', strtotime($now)),
		'message'      => $message
	));
	exit;
}

echo json_encode(array('success' => false, 'message' => 'Unknown action.'));
?>