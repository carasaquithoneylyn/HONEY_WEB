<?php
// Enrollment module data endpoints
require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* Same photo convention as module/student/ajax.php - the file lives in
   module/student/image/ regardless of which module is reading it.
   Checks tblstudent.PHOTO first, falls back to a file named after
   IDNO, falls back to default.png. */
function enrollment_student_photo_url($photo, $idno) {
	$dir = dirname(__DIR__).DIRECTORY_SEPARATOR.'student'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR;
	$web = WEB_ROOT.'module/student/image/';

	if (!empty($photo)) {
		$safe = preg_replace('/[^A-Za-z0-9_.-]/', '', (string)$photo);
		if ($safe !== '' && is_file($dir.$safe)) {
			return $web.$safe.'?v='.filemtime($dir.$safe);
		}
	}

	$idnoSafe = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$idno);
	if ($idnoSafe !== '') {
		foreach (array('jpg', 'jpeg', 'png', 'JPG', 'PNG') as $ext) {
			if (is_file($dir.$idnoSafe.'.'.$ext)) {
				return $web.$idnoSafe.'.'.$ext.'?v='.filemtime($dir.$idnoSafe.'.'.$ext);
			}
		}
	}

	return is_file($dir.'default.png') ? $web.'default.png' : '';
}

/* -----------------------------------------------------------------
   One enrollment row, for the Sectioning and Edit modals.
   ----------------------------------------------------------------- */
if ($act === 'row') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array();

	$mydb->setQuery("SELECT e.*, 
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.PHOTO,
			c.COURSE_CODE, c.COURSE_NAME, 
			sy.SCHOOL_YEAR 
		FROM `tblenrollment` e 
		JOIN `tblstudent`    s  ON s.S_ID       = e.S_ID 
		JOIN `tblcourses`    c  ON c.COURSE_ID  = e.COURSE_ID 
		JOIN `tblschoolyear` sy ON sy.SY_ID     = e.SY_ID 
		WHERE e.ENROLLMENT_ID = '".$id."' LIMIT 1");

	foreach ($mydb->loadResultList() as $r) {
		$output['ENROLLMENT_ID'] = $r->ENROLLMENT_ID;
		$output['S_ID']          = $r->S_ID;
		$output['IDNO']          = $r->IDNO;
		$output['FULLNAME']      = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);
		$output['PICTURE_URL']   = enrollment_student_photo_url($r->PHOTO, $r->IDNO);

		$output['COURSE_ID']     = $r->COURSE_ID;
		$output['COURSE_TEXT']   = $r->COURSE_CODE.' - '.$r->COURSE_NAME;
		$output['SY_ID']         = $r->SY_ID;
		$output['SCHOOL_YEAR']   = $r->SCHOOL_YEAR;
		$output['SEMESTER']      = $r->SEMESTER;
		$output['YEAR_LEVEL']    = $r->YEAR_LEVEL;
		$output['CATEGORY']      = $r->CATEGORY;
		$output['CURRICULUM_YR'] = ($r->CURRICULUM_YR === null) ? '' : $r->CURRICULUM_YR;
		$output['SECTION_ID']    = ($r->SECTION_ID === null) ? '' : $r->SECTION_ID;
		$output['STATUS']        = $r->STATUS;

		/* Both dates are nullable now. An <input type="date"> chokes on
		   anything that is not YYYY-MM-DD, so normalize to blank. */
		$output['DATE_RESERVED'] = ($r->DATE_RESERVED === null || $r->DATE_RESERVED == '0000-00-00') ? '' : substr($r->DATE_RESERVED, 0, 10);
		$output['DATE_ENROLLED'] = ($r->DATE_ENROLLED === null || $r->DATE_ENROLLED == '0000-00-00') ? '' : substr($r->DATE_ENROLLED, 0, 10);
	}

	echo json_encode($output);
	exit;
}

/* -----------------------------------------------------------------
   Sections available for a given course + school year. This is the
   Sectioning step, so it lives here rather than in the Student module.
   ----------------------------------------------------------------- */
if ($act === 'sections') {

	$course = intval($_POST['COURSE_ID']);
	$sy     = intval($_POST['SY_ID']);
	$rows   = array();

	if ($course > 0 && $sy > 0) {
		$mydb->setQuery("SELECT SECTION_ID, SECTION_NAME, YEAR_LEVEL 
			FROM `tblsections` 
			WHERE COURSE_ID = '".$course."' AND SY_ID = '".$sy."' 
			ORDER BY YEAR_LEVEL ASC, SECTION_NAME ASC");
		foreach ($mydb->loadResultList() as $row) {
			$rows[] = array(
				'SECTION_ID'   => $row->SECTION_ID,
				'SECTION_NAME' => $row->SECTION_NAME,
				'YEAR_LEVEL'   => $row->YEAR_LEVEL
			);
		}
	}

	echo json_encode($rows);
	exit;
}

/* -----------------------------------------------------------------
   DataTables list
   ----------------------------------------------------------------- */
$base = "FROM `tblenrollment` e 
	JOIN `tblstudent`     s   ON s.S_ID      = e.S_ID 
	JOIN `tblcourses`     c   ON c.COURSE_ID = e.COURSE_ID 
	JOIN `tblschoolyear`  sy  ON sy.SY_ID    = e.SY_ID 
	LEFT JOIN `tblsections` sec ON sec.SECTION_ID = e.SECTION_ID ";

$where = " WHERE 1=1 ";

/* Status filter chips above the table */
$filter = isset($_POST['status_filter']) ? trim($_POST['status_filter']) : '';
if ($filter !== '') {
	$where .= " AND e.STATUS = '".$mydb->escape_value($filter)."' ";
}

if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
	$s = $mydb->escape_value($_POST["search"]["value"]);
	$where .= " AND (s.LNAME LIKE '%".$s."%' 
				 OR s.FNAME LIKE '%".$s."%' 
				 OR s.IDNO  LIKE '%".$s."%' 
				 OR c.COURSE_CODE LIKE '%".$s."%' 
				 OR e.STATUS LIKE '%".$s."%') ";
}

/* Whitelisted sort columns, indexed the same as the <th> order in
   list.php. Never take a raw column name from the request. */
$orderCols = array(
	0 => 'e.ENROLLMENT_ID',
	1 => 's.IDNO',
	2 => 's.LNAME',
	3 => 'c.COURSE_CODE',
	4 => 'sy.SCHOOL_YEAR',
	5 => 'e.SEMESTER',
	6 => 'e.YEAR_LEVEL',
	7 => 'e.CATEGORY',
	8 => 'sec.SECTION_NAME',
	9 => 'e.DATE_RESERVED',
	10 => 'e.DATE_ENROLLED',
	11 => 'e.STATUS'
);

$orderBy = " ORDER BY e.ENROLLMENT_ID DESC ";
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

$select = "SELECT e.ENROLLMENT_ID, e.YEAR_LEVEL, e.SEMESTER, e.CATEGORY, e.CURRICULUM_YR, 
		e.DATE_RESERVED, e.DATE_ENROLLED, e.STATUS, 
		s.IDNO, s.LNAME, s.FNAME, s.MNAME, 
		c.COURSE_CODE, sy.SCHOOL_YEAR, sec.SECTION_NAME ";

$mydb->setQuery($select.$base.$where.$orderBy.$limit);
$rows = $mydb->loadResultList();

$mydb->setQuery("SELECT e.ENROLLMENT_ID ".$base.$where);
$filtered = $mydb->num_rows();

$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment`");
$total = $mydb->num_rows();

$data = array();
$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

foreach ($rows as $r) {

	$statusClass = 'secondary';
	if ($r->STATUS == 'Enrolled')   { $statusClass = 'success'; }
	if ($r->STATUS == 'Registered') { $statusClass = 'warning'; }
	if ($r->STATUS == 'Dropped')    { $statusClass = 'danger'; }
	if ($r->STATUS == 'Completed')  { $statusClass = 'info'; }

	$section = ($r->SECTION_NAME === null || $r->SECTION_NAME == '')
		? '<span class="text-muted">Not sectioned</span>'
		: htmlspecialchars($r->SECTION_NAME);

	$dateEnrolled = ($r->DATE_ENROLLED === null || $r->DATE_ENROLLED == '')
		? '<span class="text-muted">-</span>'
		: $r->DATE_ENROLLED;

	$dateReserved = ($r->DATE_RESERVED === null || $r->DATE_RESERVED == '')
		? '<span class="text-muted">-</span>'
		: $r->DATE_RESERVED;

	/* Sectioning is the action that moves a registration forward, so it is
	   highlighted while the row is still Registered and quieter afterwards. */
	$sectionBtnClass = ($r->STATUS == 'Registered') ? 'btn-primary' : 'btn-outline-primary';

	$actions = '
		<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn '.$sectionBtnClass.' btn-xs doSectioning" title="Sectioning">
			<span class="fa fa-chalkboard fw-fa"></span>
		</button>
		<button type="button" EID="'.$r->ENROLLMENT_ID.'" class="btn btn-warning btn-xs editEnrollment" title="Edit">
			<span class="fa fa-edit fw-fa"></span>
		</button>
		<a href="controller.php?action=delete&id='.$r->ENROLLMENT_ID.'" onclick="return confirm(\'Delete this enrollment record?\');">
			<button type="button" class="btn btn-danger btn-xs" title="Delete"><span class="fa fa-trash fw-fa"></span></button>
		</a>';

	$data[] = array(
		$i,
		htmlspecialchars($r->IDNO),
		htmlspecialchars(trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME)),
		htmlspecialchars($r->COURSE_CODE),
		htmlspecialchars($r->SCHOOL_YEAR),
		htmlspecialchars($r->SEMESTER),
		htmlspecialchars($r->YEAR_LEVEL),
		htmlspecialchars($r->CATEGORY),
		$section,
		$dateReserved,
		$dateEnrolled,
		'<span class="badge badge-'.$statusClass.'">'.htmlspecialchars($r->STATUS).'</span>',
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