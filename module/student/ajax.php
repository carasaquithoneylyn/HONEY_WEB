<?php 
require_once("../../include/initialize.php");
global $mydb;

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* -----------------------------------------------------------------
   STAGE 1 (Reserve): details for the Reserve Slot modal.

   The section lookup that used to live here has MOVED to
   module/enrollment/ajax.php, because sectioning is now the second
   stage and belongs to the Enrollment screen.
   ----------------------------------------------------------------- */
if ($act === 'register_info') {

	$sid = intval($_POST['UID']);
	$output = array();

	$mydb->setQuery("SELECT * FROM `tblstudent` WHERE S_ID = '".$sid."' LIMIT 1");
	foreach ($mydb->loadResultList() as $row) {
		$output['S_ID']      = $row->S_ID;
		$output['IDNO']      = $row->IDNO;
		$output['FULLNAME']  = trim($row->LNAME.', '.$row->FNAME.' '.$row->MNAME);
		$output['COURSE_ID'] = $row->COURSE_ID;
	}

	/* Default to the school year flagged Active, and offer its label as
	   the curriculum year, so the clerk is not retyping it every time. */
	$output['ACTIVE_SY']  = '';
	$output['ACTIVE_AY']  = '';
	$mydb->setQuery("SELECT SY_ID, SCHOOL_YEAR FROM `tblschoolyear` WHERE STATUS = 'Active' ORDER BY SY_ID DESC LIMIT 1");
	foreach ($mydb->loadResultList() as $row) {
		$output['ACTIVE_SY'] = $row->SY_ID;
		$output['ACTIVE_AY'] = $row->SCHOOL_YEAR;
	}

	/* A student who has never been enrolled is New, otherwise Old. Just a
	   starting suggestion, the clerk can still change it. */
	$output['SUGGEST_CATEGORY'] = 'New';
	$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE S_ID = '".$sid."' LIMIT 1");
	if ($mydb->num_rows() > 0) {
		$output['SUGGEST_CATEGORY'] = 'Old';
	}

	echo json_encode($output);
	exit;
}

if (isset($_POST['UID'])) {
	$output = array();
	$sid = intval($_POST["UID"]);
	$query =	"SELECT * FROM `tblstudent` 
		WHERE S_ID = '".$sid."' 
		LIMIT 1";
	$mydb->setQuery($query);
	$result = $mydb->loadResultList();

	foreach($result as $row)
	{ 
		$output["UID"]   = $row->S_ID;
		$output["IDNO"]  = $row->IDNO;
		$output["FNAME"] = $row->FNAME;
		$output["MNAME"] = $row->MNAME;
		$output["LNAME"] = $row->LNAME;
		$output["SEX"]   = $row->SEX;

		/* BDAY is a DATE column. An <input type="date"> only accepts
		   YYYY-MM-DD, so normalize anything odd (0000-00-00, NULL) to
		   an empty string instead of feeding the input a bad value. */
		$bday = $row->BDAY;
		if ($bday === null || $bday == '0000-00-00' || $bday == '') {
			$output["BDAY"] = '';
		} else {
			$output["BDAY"] = substr($bday, 0, 10);
		}

		/* Additional profile/contact fields, so the Edit modal can be
		   pre-filled instead of always opening blank. NULL columns come
		   back from the DB as PHP null, which json_encode renders as
		   JSON null - coerce to '' so the JS side can just drop them
		   straight into text inputs without a null check on every field. */
		$output["BPLACE"]      = $row->BPLACE      !== null ? $row->BPLACE      : '';
		$output["NATIONALITY"] = $row->NATIONALITY !== null ? $row->NATIONALITY : '';
		$output["RELIGION"]    = $row->RELIGION    !== null ? $row->RELIGION    : '';
		$output["CONTACT_NO"]  = $row->CONTACT_NO  !== null ? $row->CONTACT_NO  : '';
		$output["EMAIL"]       = $row->EMAIL       !== null ? $row->EMAIL       : '';
		$output["HOME_ADD"]    = $row->HOME_ADD    !== null ? $row->HOME_ADD    : '';
		$output["STATUS"]      = $row->STATUS      !== null ? $row->STATUS      : 'Active';

		/* Resolve the student's current photo the same way view.php does,
		   so the Edit modal preview shows the real photo instead of the
		   default silhouette, and so "cancel the file picker" in the Edit
		   modal reverts to this instead of losing the photo visually. */
		$output["PHOTO_URL"] = '';
		if (!empty($row->PHOTO)) {
			$safe = preg_replace('/[^A-Za-z0-9_.-]/', '', (string)$row->PHOTO);
			$dir  = __DIR__.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR;
			if ($safe !== '' && is_file($dir.$safe)) {
				$output["PHOTO_URL"] = WEB_ROOT.'module/student/image/'.$safe.'?v='.filemtime($dir.$safe);
			}
		}
	}
	echo json_encode($output);
}else{
	$output = array();
	$query = "SELECT `S_ID`, `LNAME`, `FNAME`, `MNAME`, `SEX`, `BDAY` FROM `tblstudent`";

	if(isset($_POST["search"]["value"]))
	{
	$query .= " where `LNAME` LIKE '%".$_POST["search"]["value"]."%' ";
	}
	if(isset($_POST["order"]))
	{
		$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= 'ORDER BY `S_ID` DESC ';
	}
	if($_POST["length"] != -1)
	{
		$query .= " LIMIT " . $_POST['start'] . ", " . $_POST['length'] . "";
	}
	$mydb->setQuery($query);
	$cur = $mydb->loadResultList();
	$data = array();
	$filtered_rows = $mydb->num_rows();
	$i = 1;	
	foreach ($cur as $result) {
	$sub_array = array();
		
		$sub_array[] =$i;
	
		$sub_array[] = $result->LNAME;
		$sub_array[] = $result->FNAME;
		$sub_array[] = $result->MNAME;
		$sub_array[] = $result->SEX;
		$sub_array[] = $result->BDAY;
		
		$sub_array[] = '

		<button type="button" name="update" UID="'.$result->S_ID.'" class="btn btn-warning btn-xs editEntry" title="Edit"><span class="fa fa-edit fw-fa"></span></button> 
		
		<a href="index.php?view=view&id='.$result->S_ID.'"><button type="button" class="btn btn-info btn-xs" title="View"><span class="fa fa-eye"></span></button></a>

		<button type="button" UID="'.$result->S_ID.'" class="btn btn-success btn-xs registerEntry" title="Reserve Enrollment Slot"><span class="fa fa-clipboard-check fw-fa"></span></button>

		<a href="controller.php?action=delete&id='.$result->S_ID .'"><button type="button" class="btn btn-danger btn-xs SaveReg" title="Delete"><span class="fa fa-trash fw-fa"></span></button></a>


		';
		$data[] = $sub_array;
	$i = $i + 1;		
	}
	function get_total_all_records()
	{
		global $mydb;
		$statement = "SELECT `S_ID`, `LNAME`, `FNAME`, `MNAME`, `SEX`, `BDAY` FROM `tblstudent`";
		$mydb->setQuery($statement);
		return $mydb->num_rows();
	}

	$output = array('data' 			   => $data, 
					"recordsTotal"	   => $filtered_rows,
					"recordsFiltered"	=>	get_total_all_records() );
	echo json_encode($output);
}
?>