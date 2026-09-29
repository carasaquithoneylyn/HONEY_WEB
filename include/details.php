<?php
require_once(LIB_PATH.DS.'database.php');
class Details{
	
	protected static $tbl_name = "alumni_details";
	function db_fields(){
		global $mydb;
		return $mydb->getFieldsOnOneTable(self::$tbl_name);
	}
	function listOfUsers(){
		global $mydb;
		$mydb->setQuery("Select * from ".self::$tbl_name);
		$cur = $mydb->loadResultList();
		return $cur;
	
	}
	function single_user($id=''){
			global $mydb;
			$mydb->setQuery("SELECT * FROM ".self::$tbl_name." Where PK= '{$id}' LIMIT 1");
			$cur = $mydb->loadSingleResult();
			return $cur;
	}
	function find_all_InstitutionName($name=""){
			global $mydb;
			$mydb->setQuery("SELECT * 
							FROM  ".self::$tbl_name." 
							WHERE  `InstitutionName` ='{$name}'");
			$row_count = $mydb->num_rows();
			return $row_count;
	}
	function find_all_user_notthis($name="", $uid=0){
			global $mydb;
			$mydb->setQuery("SELECT * 
							FROM  ".self::$tbl_name." 
							WHERE  `USERNAME` ='{$name}' and UID != ". $uid. " ");
			$row_count = $mydb->num_rows();
			return $row_count;
	}
	function find_oldpass($pk, $oldpass){
			global $mydb;
			$mydb->setQuery("SELECT * FROM ".self::$tbl_name." Where 
				`PK`=" . $pk . " and Password = sha1('{$oldpass}') LIMIT 1");
		return	$row_count = $mydb->num_rows();
			
	}

	 static function AuthenticateUser($username="", $h_upass=""){
		global $mydb;
		$mydb->setQuery("SELECT * FROM `tblusers` WHERE `USERNAME`='" . $username . "' and `PASSWORD`='" . $h_upass ."' AND STATUSACTIVE = 1 LIMIT 1");
		$row_count = $mydb->num_rows();//get the number of count
		 if ($row_count == 1){
		 $found_user = $mydb->loadSingleResult();
		 $mydb->setQuery("SELECT * FROM `tblacademicyear` WHERE `IsDefault` = 1");
		 $cur1 = $mydb->loadSingleResult();
		    $_SESSION['UID'] 	 	= $found_user->UID;
            $_SESSION['DISPLAYNAME'] = $found_user->DISPLAYNAME;
            $_SESSION['USERNAME']	= $found_user->USERNAME;
            $_SESSION['TYPE']    	= $found_user->TYPE;
            $_SESSION['default']    	= $cur1->ACADYEAR;
            $_SESSION['SEMESTER']    	= $cur1->SEMESTER;
            //$_SESSION['default']    	= $found_user->TYPE;
        	return true;
			}else{
				return false;
			}	
				
	} 	
	
	/*---Instantiation of Object dynamically---*/
	static function instantiate($record) {
		$object = new self;

		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		} 
		return $object;
	}
	
	
	/*--Cleaning the raw data before submitting to Database--*/
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
	  global $mydb;
	  $attributes = array();
	  foreach($this->db_fields() as $field) {
	    if(property_exists($this, $field)) {
			$attributes[$field] = $this->$field;
		}
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $mydb;
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $mydb->escape_value($value);
	  }
	  return $clean_attributes;
	}
	
	/*--Create,Update and Delete methods--*/
	public function save() {
	  // A new record won't have an id yet.
	  return isset($this->id) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $mydb;
		// Don't forget your SQL syntax and good habits:
		// - INSERT INTO table (key, key) VALUES ('value', 'value')
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".self::$tbl_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		return	$mydb->InsertThis($sql);
	}

	public function update($id=0) {
	  global $mydb;
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
		  $attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$tbl_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE AlumniID =". $id;
		return  $mydb->InsertThis($sql);
	 	
	}

	public function delete($id=0) {
		global $mydb;
		  $sql = "DELETE FROM ".self::$tbl_name;
		  $sql .= " WHERE AlumniID =". $id;
		  $sql .= " LIMIT 1 ";
		return  $mydb->InsertThis($sql);
		  
	}
		
}
?>