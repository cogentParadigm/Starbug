<?php
/**
* FILE: core/db/Table.php
* PURPOSE: This class wraps a databse table, it is the base class for database models
*
* This file is part of StarbugPHP
*
* StarbugPHP - website development kit
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
class Table {

	var $type;
	var $filters;
	var $relations;
	var $record_count;
	var $insert_id;
	var $imported;
	var $imported_functions; 

	function Table($type, $filters=array()) {
		$this->type = $type;
		if (!isset($this->filters)) $this->filters = $filters;
		$this->imports = array();
		$this->imported_functions = array();
	}
	
	protected function has_one($name, $lookup, $ref_field="") {
		if (empty($ref_field)) {
			$ref_field = $lookup;
			$lookup = $this->type;
		}
		$this->relations[$name] = array("type" => "one", "lookup" => $lookup, "ref" => $ref_field);
	}
	
	protected function has_many($name, $hook, $lookup="", $ref_field="") {
		$this->relations[$name] = array("type" => "many", "hook" => $hook);
		if ($lookup && $ref_field) {
			$this->relations[$name]["lookup"] = $lookup;
			$this->relations[$name]["ref"] = $ref_field;
		}
	}

	protected function store($arr) {
		global $sb;
		$errors = $sb->store($this->type, $arr, $this->filters);
		if ((empty($errors)) && (empty($arr['id']))) $this->insert_id = $sb->insert_id;
		return $errors;
	}

	protected function remove($where) {
		global $sb;
		return $sb->remove($this->type, $where);
	}
	
	function query($args="", $froms="", $deep="auto") {
		global $sb;
		$records = $sb->query($this->type.((empty($froms)) ? "" : ", ".$froms), $args, (($deep=="auto") ? (!empty($froms)) : $deep));
		$this->record_count = $sb->record_count;
		return $records;
	}
	
	function grant() {
		global $sb;
		$_POST[$this->type]['status'] = array_sum($_POST['status']);
		$sb->grant($this->type, $_POST[$this->type]);
	}

	protected function mixin($object) {
		$new_import = new $object();
		$import_name = get_class($new_import);  
		$import_functions = get_class_methods($new_import);  
		array_push($this->imported, array($import_name, $new_import));  
		foreach($import_functions as $key => $function_name) $this->imported_functions[$function_name] = &$new_import;
	}

	public function __call($method, $args) {  
		if(array_key_exists($method, $this->imported_functions)) {  
			$args[] = $this;  
			return call_user_func_array(array($this->imported_functions[$method], $method), $args);
		}
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}  

}
?>
