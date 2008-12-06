<?php
load_file("util/Validate");
class Table {

	var $db;
	var $type;
	var $uniques;
	var $defaults;
	var $lengths;
	var $recordCount;

	function Table($data, $t, $u=array(), $d=array(), $l=array()) {
		$this->db = $data;
		$this->type = $t;
		if (!isset($this->uniques)) $this->uniques = $u;
		if (!isset($this->defaults)) $this->defaults = $d;
		if (!isset($this->lengths)) $this->lengths = $l;
	}

	protected function store($arr) {
		$errors = array();
		foreach ($arr as $col => $value) {
			//echo $col." ".$value."\n";
			$arr[$col] = Validate::toStore(trim($value));
			if ($arr[$col] === "") {
				if (isset($this->defaults[$col])) $arr[$col] = $this->defaults[$col];
				else $errors[$col."Error"] = true; //missing required var $col
			}
			if (isset($this->lengths[$col])) { //check length
				$length = split(":", $this->lengths[$col]);
				if (!Validate::length($arr[$col], $length[0], $length[1])) $errors[$col."LengthError"] = true; //$col is too long
			}
		}
		foreach ($this->uniques as $val) {$this->get($val, $val."='".$arr[$val]."'"); if($this->recordCount > 0) $errors[$val."ExistsError"] = true;}
		if(empty($errors)) { //no errors
			if(!empty($arr['id'])) { //updating existing record
				foreach($arr as $col => $value) {
					if ($col != 'id') {
						if(empty($setstr)) $setstr = $col."='".$value."'";
						else $setstr .= ", ".$col."='".$value."'";
					}
				}
				$this->db->Execute("UPDATE ".P($this->type)." SET ".$setstr." WHERE id='".$arr['id']."'");
			} else { //creating new record
				$keys = ""; $values = "";
				foreach($arr as $col => $value) {
					if(empty($keys)) $keys = $col;
					else $keys .= ", ".$col;
					if ($value != 'DATETIME()') $value = "'".$value."'";
					if(empty($values)) $values = $value;
					else $values .= ", ".$value;
				}
				//echo "INSERT INTO ".P($this->type)." (".$keys.") VALUES (".$values.")";
				$this->db->Execute("INSERT INTO ".P($this->type)." (".$keys.") VALUES (".$values.")");
			}
		}
		return $errors;
	}

	protected function remove($where) {
		if (!empty($where)) {
			$records = $this->db->Execute("DELETE FROM ".P($this->type)." WHERE ".$where);
			$this->recordCount = $records->RecordCount();
			return $records;
		}
	}

	function find($select, $where="", $other="") {
		$_SESSION[P("security")] = empty_nan($_SESSION[P("security")], 1);
		$securityQuery = "(security<=".$_SESSION[P("security")];
		if($_SESSION[P("security")] != 1) $securityQuery .= " && security!=1)";
		else $securityQuery .= ")";
		if (!empty($where)) $where = $securityQuery." && ".$where;
		else $where = $securityQuery;
		return $this->get($select, $where, $other);
	}

	function get($select, $where="", $other="") {
		$whereclause = ((empty($where)) ? "" : " WHERE ".$where);
		if (!empty($other)) $whereclause .= " ".$other;
		//echo "finding ".$select." FROM ".P($this->type).$whereclause;
		$records = $this->db->Execute("SELECT ".$select." FROM ".P($this->type).$whereclause);
		$this->recordCount = $records->RecordCount();
		return $records;
	}

	function afind($select, $where="", $other="") {return $this->to_array($this->find($select, $where, $other));}

	function aget($select, $where="", $other="") {return $this->to_array($this->get($select, $where, $other));}

	function to_array($records) {
		$arr = array();
		while (!$records->EOF) {
			$arr[] = $records->fields;
			$records->MoveNext();
		}
		$records->Close();
		return $arr;
	}

}
?>
