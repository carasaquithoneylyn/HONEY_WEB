<?php

// ABADIES SOLUTIONS

require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['COURSE_ID'])) {
	$output = array();
	$query =	"SELECT * FROM tblcourses
		WHERE COURSE_ID = '".$_POST["COURSE_ID"]."'
		LIMIT 1";
	$mydb->setQuery($query);
	$result = $mydb->loadResultList();

	foreach($result as $row)
	{
		$output["COURSE_ID"] = $row->COURSE_ID;
		$output["COURSE_CODE"] = $row->COURSE_CODE;
		$output["COURSE_NAME"] = $row->COURSE_NAME;
		$output["COURSE_DESC"] = $row->COURSE_DESC;
		$output["STATUS"] = $row->STATUS;
	}
	echo json_encode($output);
}else{
	$output = array();
	$query = "SELECT * FROM `tblcourses` ";

	if(isset($_POST["search"]["value"]))
	{
	$query .= " where `COURSE_NAME` LIKE '%".$_POST["search"]["value"]."%' OR `COURSE_CODE` LIKE '%".$_POST["search"]["value"]."%' ";
	}
	if(isset($_POST["order"]))
	{
		$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= 'ORDER BY `COURSE_NAME` ASC ';
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
		$sub_array[] = $result->COURSE_CODE;
		$sub_array[] = $result->COURSE_NAME;
		$sub_array[] = $result->COURSE_DESC;
		$sub_array[] = $result->STATUS;
		$sub_array[] = '
		<a href="index.php?view=view&id='.$result->COURSE_ID.'"><button type="button" class="btn btn-success btn-xs" title="View"><span class="fa fa-eye"></span></button></a>
		<button type="button" name="update" COURSE_ID="'.$result->COURSE_ID .'" class="btn btn-warning btn-xs editEntry"><span class="fa fa-edit fw-fa"></span></button>
		<button type="button" name="delete" COURSE_ID="'.$result->COURSE_ID .'" class="btn btn-danger btn-xs deleteEntry"><span class="fa fa-trash"></span></button>';
		$data[] = $sub_array;
	$i = $i + 1;
	}
	function get_total_all_records()
	{
		global $mydb;
		$statement = "SELECT * FROM `tblcourses`";
		$mydb->setQuery($statement);
		return $mydb->num_rows();
	}

	$output = array('data' 			   => $data,
					"recordsTotal"	   => $filtered_rows,
					"recordsFiltered"	=>	get_total_all_records() );
	echo json_encode($output);
}
?>
