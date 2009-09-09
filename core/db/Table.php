<?php
/**
* FILE: core/db/Table.php
* PURPOSE: This class wraps Adodb lite, it is the base class for database models
*
* This file is part of StarbugPHP
*
* StarbugPHP - meta content manager
* Copyright (C) 2008-2009 Ali Gangji
*
* StarbugPHP is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* StarbugPHP is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with StarbugPHP.  If not, see <http://www.gnu.org/licenses/>.
*/
include("util/Validate.php");
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
			//echo $col." '".$value."'\n";
			$arr[$col] = trim($value);
			if ($arr[$col] === "") {
				if (isset($this->defaults[$col])) $arr[$col] = $this->defaults[$col];
				else $errors[$col."Error"] = true; //missing required var $col
			}
			if (isset($this->lengths[$col])) { //check length
				$length = split(":", $this->lengths[$col]);
				if (!next($length)) $length = array(0, $length[0]);
				if (!Validate::length($arr[$col], $length[0], $length[1])) $errors[$col."LengthError"] = true; //$col is too long
			}
		}
		foreach ($this->uniques as $val) {
			$urow = $this->get("id, ".$val, $val."='".$arr[$val]."'")->fields();
			if (((!empty($arr['id'])) || ($this->recordCount != 0)) && ((empty($arr['id'])) || ($this->recordCount > 1) || ($arr['id'] != $urow['id']))) $errors[$val."ExistsError"] = true;
		}
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
		if (($_SESSION[P('memberships')] & 1)==1) return $this->get($select, $where, $other);
		else return $this->get_object_permits($select, "read", $where, $other);
	}
	
	function get_table_permits($action) {
		$sql = "SELECT * FROM ".P("permits")." AS p WHERE p.priv_type='table' && p.related_table='".P($this->type)."' && action='$action' && (p.role='everyone'";
		$sql .= " || (p.role='user' && p.who='".$_SESSION[P('id')]."') || (p.role='group' && (('".$_SESSION[P('memberships')]."' & p.who)=p.who)))";
		$records = $this->db->Execute($sql);
		$this->recordCount = $records->RecordCount();
		return $records;
	}

	function get_object_permits($select, $action, $query="", $other="") {
		if (!empty($query)) $query .= " && ";
		if (!empty($other)) $other = " ".$other;
		$sql = "SELECT DISTINCT $select FROM ".P("permits")." AS p, ".P($this->type)." AS obj WHERE ".$query."p.related_table='".P($this->type)."' && p.action='$action' && (p.priv_type='global' || (p.priv_type='object' && p.related_id=obj.id)) && ((p.status & obj.status)=obj.status) && (p.role='everyone'";
		$sql .= " || (p.role='user' && p.who='".$_SESSION[P('id')]."') || (p.role='group' && (('".$_SESSION[P('memberships')]."' & p.who)=p.who)) || (p.role='owner' && obj.owner='".$_SESSION[P('id')]."') || (p.role='collective' && (('".$_SESSION[P('memberships')]."' & obj.collective)=obj.collective)))";
		$records = $this->db->Execute($sql.$other);
		$this->recordCount = $records->RecordCount();
		return $records;
	}

	function get($select, $where="", $other="") {
		$whereclause = ((empty($where)) ? "" : " WHERE ".$where);
		if (!empty($other)) $whereclause .= " ".$other;
		//echo "finding ".$select." FROM ".P($this->type).$whereclause;
		$records = $this->db->Execute("SELECT ".$select." FROM ".P($this->type).$whereclause);
		$this->recordCount = $records->RecordCount();
		return $records;
	}

}
?>
