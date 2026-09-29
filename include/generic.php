<?php
// ABADIES SOLUTIONS

require_once(LIB_PATH.DS.'database.php');

class GenericRecord {

	public $table;
	public $pk;
	protected $columns = null; // cached DESC output

	function __construct($table, $pk = null) {
		global $mydb;
		$this->table = $table;
		$this->pk    = $pk ? $pk : $mydb->getPrimaryKey($table);
	}

	/* Full column description: Field, Type, Null, Key, Default, Extra */
	function columns() {
		global $mydb;
		if ($this->columns === null) {
			$this->columns = $mydb->describeTable($this->table);
		}
		return $this->columns;
	}

	function fieldNames() {
		$names = array();
		foreach ($this->columns() as $c) { $names[] = $c->Field; }
		return $names;
	}


	/* Returns the DESC row for one column, or null. */
	function columnMeta($field) {
		foreach ($this->columns() as $c) {
			if ($c->Field == $field) { return $c; }
		}
		return null;
	}

	/* An HTML form always posts a value, even an empty one. Writing ''
	   into a DATE column produces 0000-00-00 (or a hard error under
	   strict SQL mode) and stops the column DEFAULT from ever applying.
	   This decides what an empty value really means:
	     'null' -> column accepts NULL, write NULL
	     'skip' -> column has a DEFAULT, leave it out so MySQL fills it
	     'keep' -> write the empty string as-is                         */
	function emptyValueMode($field) {
		$col = $this->columnMeta($field);
		if (!$col) { return 'keep'; }
		if (strtoupper($col->Null) === 'YES') { return 'null'; }
		if ($col->Default !== null && $col->Default !== '') { return 'skip'; }
		$t = strtolower($col->Type);
		if (strpos($t, 'date') === 0 || strpos($t, 'timestamp') === 0) { return 'skip'; }
		return 'keep';
	}

	function isAutoIncrement($field) {
		foreach ($this->columns() as $c) {
			if ($c->Field == $field) {
				return stripos($c->Extra, 'auto_increment') !== false;
			}
		}
		return false;
	}

	/* All rows, most recent first when the PK looks numeric/auto-increment */
	function listAll() {
		global $mydb;
		$mydb->setQuery("SELECT * FROM `".$this->table."` ORDER BY `".$this->pk."` DESC");
		return $mydb->loadResultList();
	}

	function single($id) {
		global $mydb;
		$mydb->setQuery("SELECT * FROM `".$this->table."` WHERE `".$this->pk."` = '".$mydb->escape_value($id)."' LIMIT 1");
		return $mydb->loadSingleResult();
	}

	function countAll() {
		global $mydb;
		$mydb->setQuery("SELECT * FROM `".$this->table."`");
		return $mydb->num_rows();
	}

	/* $data is an associative array [column => value]. Only columns that
	   really exist on the table are used, and the auto-increment PK (if any)
	   is always skipped so MySQL can assign it. */
	function create($data) {
		global $mydb;
		$valid = $this->fieldNames();
		$cols = array();
		$vals = array();
		foreach ($data as $key => $value) {
			if (!in_array($key, $valid)) { continue; }
			if ($key == $this->pk && $this->isAutoIncrement($this->pk)) { continue; }
			if ($value === '' || $value === null) {
				$mode = $this->emptyValueMode($key);
				if ($mode === 'skip') { continue; }
				if ($mode === 'null') { $cols[] = "`".$key."`"; $vals[] = "NULL"; continue; }
			}
			$cols[] = "`".$key."`";
			$vals[] = "'".$mydb->escape_value($value)."'";
		}
		if (empty($cols)) { return false; }
		$sql = "INSERT INTO `".$this->table."` (".join(", ", $cols).") VALUES (".join(", ", $vals).")";
		return $mydb->InsertThis($sql);
	}

	function update($id, $data) {
		global $mydb;
		$valid = $this->fieldNames();
		$pairs = array();
		foreach ($data as $key => $value) {
			if (!in_array($key, $valid)) { continue; }
			if ($key == $this->pk) { continue; }
			if ($value === '' || $value === null) {
				$mode = $this->emptyValueMode($key);
				if ($mode === 'skip') { continue; }
				if ($mode === 'null') { $pairs[] = "`".$key."` = NULL"; continue; }
			}
			$pairs[] = "`".$key."` = '".$mydb->escape_value($value)."'";
		}
		if (empty($pairs)) { return false; }
		$sql = "UPDATE `".$this->table."` SET ".join(", ", $pairs)." WHERE `".$this->pk."` = '".$mydb->escape_value($id)."'";
		return $mydb->InsertThis($sql);
	}

	function delete($id) {
		global $mydb;
		$sql = "DELETE FROM `".$this->table."` WHERE `".$this->pk."` = '".$mydb->escape_value($id)."' LIMIT 1";
		return $mydb->InsertThis($sql);
	}
}
?>
