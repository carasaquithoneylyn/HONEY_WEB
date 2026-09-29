<?php

//ABADIES SOLUTIONS

require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['UID'])) {
	$output = array();
	$query =	"SELECT * FROM tblusers 
		WHERE UID = '".$_POST["UID"]."' 
		LIMIT 1";
	$mydb->setQuery($query);
	$result = $mydb->loadResultList();

	foreach($result as $row)
	{ 
		$output["UID"] = $row->UID;
		$output["DISPLAYNAME"] = $row->DISPLAYNAME;
		$output["USERNAME"] = $row->USERNAME;
		$output["TYPE"] = $row->TYPE;
		$output["STATUSACTIVE"] = $row->STATUSACTIVE;
				
	}
	echo json_encode($output);
}else{
	$output = array();
	$query = "SELECT * FROM `tblusers` ";

	if(isset($_POST["search"]["value"]))
	{
	$query .= " where `USERNAME` LIKE '%".$_POST["search"]["value"]."%' ";
	}
	if(isset($_POST["order"]))
	{
		$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= 'ORDER BY `UID` DESC ';
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
				// `UID`, `USERNAME`, `PASSWORD`, `TYPE`, `FULLNAME`, `DOB`, `AGE`, `SEX`, `ADDRESS`, `PICTURE`, `ADDEDBY`, `DATEADDED`, `MODIFIEDBY`, `DATEMODIFIED`
	$sub_array = array();
		
		$sub_array[] =$i;
		$sub_array[] = $result->EMPID;
		$sub_array[] = $result->DISPLAYNAME;
		$sub_array[] = $result->USERNAME;
		$sub_array[] = $result->TYPE;
		$sub_array[] = $result->DATEADDED;
		$sub_array[] = $result->DATEMODIFIED;
		$sub_array[] = '
		<button type="button" name="update" UID="'.$result->UID.'" class="btn btn-warning btn-xs editEntry"><span class="fa fa-edit fw-fa"></span></button> 
		<button  type="button" name="changepass" UID="'.$result->UID.'" class="btn btn-info btn-xs changepass"><span class="fa fa-trash-o fa-key"></span></button> ';
		$data[] = $sub_array;
	$i = $i + 1;		
	}
	function get_total_all_records()
	{
		global $mydb;
		$statement = "SELECT * FROM `tblusers`";
		$mydb->setQuery($statement);
		return $mydb->num_rows();
	}

	$output = array('data' 			   => $data, 
					"recordsTotal"	   => $filtered_rows,
					"recordsFiltered"	=>	get_total_all_records() );
	echo json_encode($output);
}
?>