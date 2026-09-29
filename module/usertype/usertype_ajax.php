<?php 

//ABADIES SOLUTIONS

require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['TYPEID'])) {
	$output = array();
	$query =	"SELECT * FROM tblusertype 
		WHERE TYPEID = '".$_POST["TYPEID"]."' 
		LIMIT 1";
	$mydb->setQuery($query);
	$result = $mydb->loadResultList();

	foreach($result as $row)
	{ 
		$output["TYPEID"] = $row->TYPEID;
		$output["USERTYPE"] = $row->USERTYPE;
		$output["STATUS"] = $row->STATUS;
	}
	echo json_encode($output);
}elseif (isset($_POST['id'])) {
	$Permission = new Permission();
	$Permission->AllowOpen = $_POST['ischeck'];
	$Permission->updatePermission($_POST['id']);
}else{
	$output = array();
	$query = "SELECT * FROM `tblusertype` ";

	if(isset($_POST["search"]["value"]))
	{
	$query .= " where `USERTYPE` LIKE '%".$_POST["search"]["value"]."%' ";
	}
	if(isset($_POST["order"]))
	{
		$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= 'ORDER BY `USERTYPE` DESC ';
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
		$sub_array[] = $result->USERTYPE;
		$sub_array[] = $result->STATUS;
		$sub_array[] = '
		<button type="button" name="update" TYPEID="'.$result->TYPEID .'" class="btn btn-warning btn-xs editEntry"><span class="fa fa-edit fw-fa"></span></button>
		<button type="button" name="delete" TYPEID="'.$result->TYPEID .'" class="btn btn-danger btn-xs deleteEntry"><span class="fa fa-trash"></span></button>';
		$data[] = $sub_array;
	$i = $i + 1;		
	}
	function get_total_all_records()
	{
		global $mydb;
		$statement = "SELECT * FROM `tblusertype`";
		$mydb->setQuery($statement);
		return $mydb->num_rows();
	}

	$output = array('data' 			   => $data, 
					"recordsTotal"	   => $filtered_rows,
					"recordsFiltered"	=>	get_total_all_records() );
	echo json_encode($output);
}
?>