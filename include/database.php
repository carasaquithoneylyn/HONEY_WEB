<?php
require_once(LIB_PATH.DS."config.php");
class Database {
	var $sql_string = '';
	var $error_no = 0;
	var $error_msg = '';
	var $query = '';
	public $conn;
	public $last_query;
	private $magic_quotes_active;
	private $real_escape_string_exists;
	
	function __construct() {
		$this->open_connection();
		$this->magic_quotes_active = function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc();
		$this->real_escape_string_exists = function_exists("mysql_real_escape_string");
	}
	
	public function open_connection() {
		try {
		$this->conn = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_NAME. "", DB_USER,DB_PASS);
		// set the PDO error mode to exception
		$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//echo "Connected successfully";
		}catch(PDOException $e){
			echo "Problem in database connection! Contact administrator!". $e->getMessage();
		
		}

	}
	
	/* ABADIES SOLUTIONS - run an INSERT / UPDATE / DELETE.

	   PDO here runs in ERRMODE_EXCEPTION, so a failing statement used to
	   throw straight out of this method. Nothing caught it, so the page
	   died mid-request and the browser showed a blank error screen with no
	   clue what went wrong. Now the exception is caught, the reason is kept
	   in $error_msg for lastError(), and the caller simply gets false and
	   can show a normal message. */
	function InsertThis($sql='') {
		$this->sql_string = $sql;
		$this->error_msg  = '';
		$this->error_no   = 0;

		try {
			$this->query = $this->conn->prepare($this->sql_string);
			return $this->query->execute() ? true : false;
		} catch (PDOException $e) {
			$this->error_msg = $e->getMessage();
			$this->error_no  = (int)$e->getCode();
			error_log("InsertThis failed: ".$this->error_msg." | SQL: ".$this->sql_string);
			return false;
		}
	}

	/* Short, readable reason the last InsertThis() returned false.
	   Safe to append to an on-screen message. */
	public function lastError() {
		if ($this->error_msg == '') { return ''; }
		if (preg_match("/Column '([^']+)' cannot be null/i", $this->error_msg, $m)) {
			return "(".$m[1]." is required.)";
		}
		if (strpos($this->error_msg, '1062') !== false || stripos($this->error_msg, 'Duplicate entry') !== false) {
			return "(That value is already used by another record.)";
		}
		if (stripos($this->error_msg, 'foreign key constraint') !== false) {
			return "(Another record still depends on this one.)";
		}
		return "(Database said: ".$this->error_msg.")";
	}
	
	function setQuery($sql='') {
		try {
		   $this->sql_string=$sql;
			$this->query = $this->conn->prepare($this->sql_string);
			$this->query->execute();

		  } catch (PDOException $e) {
		    echo "Failed to get query handle: " . $e->getMessage() . "\n";
		    exit;
		  }
		
	}
	
	function loadResultList() {
		
		$results = $this->query->fetchAll(PDO::FETCH_OBJ);
		return $results;
	}	
	function loadSingleResultAssoc() {
		
		$results = $this->query->fetch(PDO::FETCH_ASSOC);
		return $results;
	}	
	
	public function num_rows() {
		return $this->query->rowCount();
	}


	function loadSingleResult() {
		
		$results = $this->query->fetch(PDO::FETCH_OBJ);
		return $results;
	}
	
	function getFieldsOnOneTable( $tbl_name ) {
	
		$this->setQuery("DESC ".$tbl_name);
		$rows = $this->loadResultList();
		
		$f = array();
		for ( $x=0; $x<count( $rows ); $x++ ) {
			$f[] = $rows[$x]->Field;
		}
		
		return $f;
	}	

	/* ABADIES SOLUTIONS - full column description (Field, Type, Null, Key, Default, Extra)
	   used by the generic table module so it can build lists/forms for ANY table
	   in the database without needing to hard-code its columns. */
	function describeTable( $tbl_name ) {
		$this->setQuery("DESC `".str_replace('`','',$tbl_name)."`");
		return $this->loadResultList();
	}

	/* Returns the name of the PRIMARY KEY column of a table, falling back to
	   the first column if none is flagged PRI (some legacy tables in this
	   project don't declare one). */
	function getPrimaryKey( $tbl_name ) {
		$cols = $this->describeTable($tbl_name);
		foreach ($cols as $c) {
			if ($c->Key === 'PRI') {
				return $c->Field;
			}
		}
		return isset($cols[0]) ? $cols[0]->Field : null;
	}

	/* Returns true if the table actually exists in the current database -
	   used so the generic module never runs a query against a bogus name. */
	function tableExists( $tbl_name ) {
		$this->setQuery("SHOW TABLES LIKE '".str_replace(array('`',"'"),'',$tbl_name)."'");
		return $this->num_rows() > 0;
	}

	public function fetch_array($result) {
		return mysqli_fetch_array($result);
	}
	//gets the number or rows	

	public function insert_id() {
    // get the last id inserted over the current db connection
		return $this->conn->lastInsertId();
	}
  
	public function affected_rows() {
		return mysqli_affected_rows($this->conn);
	}
	
	 public function escape_value( $value ) {
		if( $this->real_escape_string_exists ) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $this->magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysql_real_escape_string( $value );
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$this->magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
   	}
	
	public function close_connection() {
		$conn = null;
	}
	
} 
$mydb = new Database();


?>