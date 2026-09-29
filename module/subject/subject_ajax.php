<?php

// ABADIES SOLUTIONS

require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['SUBJECT_ID'])) {
	$output = array();
	$query =	"SELECT * FROM tblsubjects
		WHERE SUBJECT_ID = '".$_POST["SUBJECT_ID"]."'
		LIMIT 1";
	$mydb->setQuery($query);
	$result = $mydb->loadResultList();

	foreach($result as $row)
	{
		$output["SUBJECT_ID"] = $row->SUBJECT_ID;
		$output["SUBJECT_CODE"] = $row->SUBJECT_CODE;
		$output["SUBJECT_NAME"] = $row->SUBJECT_NAME;
		$output["UNITS"] = $row->UNITS;
		$output["COURSE_ID"] = $row->COURSE_ID;
		$output["YEAR_LEVEL"] = $row->YEAR_LEVEL;
		$output["SEMESTER"] = $row->SEMESTER;
	}
	echo json_encode($output);
}else{
	$output = array();
	$query = "SELECT s.*, c.COURSE_CODE, c.COURSE_NAME
			   FROM `tblsubjects` s
			   LEFT JOIN `tblcourses` c ON c.COURSE_ID = s.COURSE_ID ";

	if(isset($_POST["search"]["value"]))
	{
	$query .= " where s.`SUBJECT_NAME` LIKE '%".$_POST["search"]["value"]."%' OR s.`SUBJECT_CODE` LIKE '%".$_POST["search"]["value"]."%' ";
	}
	if(isset($_POST["order"]))
	{
		$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= 'ORDER BY s.`SUBJECT_NAME` ASC ';
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

		$sub_array[] = $i;
		$sub_array[] = $result->SUBJECT_CODE;
		$sub_array[] = $result->SUBJECT_NAME;
		$sub_array[] = $result->UNITS;
		$sub_array[] = isset($result->COURSE_CODE) ? $result->COURSE_CODE : '';
		$sub_array[] = $result->YEAR_LEVEL;
		$sub_array[] = $result->SEMESTER;
		$sub_array[] = '
		<button type="button" name="update" SUBJECT_ID="'.$result->SUBJECT_ID .'" class="btn btn-warning btn-xs editEntry"><span class="fa fa-edit fw-fa"></span></button>
		<button type="button" name="delete" SUBJECT_ID="'.$result->SUBJECT_ID .'" class="btn btn-danger btn-xs deleteEntry"><span class="fa fa-trash"></span></button>';
		$data[] = $sub_array;
	$i = $i + 1;
	}
	function get_total_all_records()
	{
		global $mydb;
		$statement = "SELECT * FROM `tblsubjects`";
		$mydb->setQuery($statement);
		return $mydb->num_rows();
	}

	$output = array('data' 			   => $data,
					"recordsTotal"	   => $filtered_rows,
					"recordsFiltered"	=>	get_total_all_records() );
	echo json_encode($output);
}
?>
