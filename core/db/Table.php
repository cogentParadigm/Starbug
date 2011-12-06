<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of Starbug PHP
 * @file core/db/Table.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Table
 */
/**
 * @defgroup Table
 * the db class
 * @ingroup db
 */
/**
 * This class wraps a databse table, it is the base class for database models
 * @ingroup Table
 */
class Table {
	/**
	 * @var string The unprefixed table name
	 */
	var $type;
	/**
	 * @var array The filters to apply to each column before attempting to store
	 */
	var $filters;
	/**
	 * @var array The relationships to other tables
	 */
	var $relations;
	/**
	 * @var int The number of records returned by the last query
	 */
	var $record_count;
	/**
	 * @var int The id of the last record inserted
	 */
	var $insert_id;
	/**
	 * @var array The mixed-in objects which hold the imported functions
	 */
	var $imported;
	/**
	 * @var array A list of the imported functions
	 */
	var $imported_functions;

	/**
	 * Table constructor
	 * @param string $type the un-prefixed table name
	 * @param array $filters the column filters
	 */
	function __construct($type, $filters=array()) {
		$this->type = $type;
		if (!isset($this->filters)) $this->filters = $filters;
		$this->imported = array();
		$this->imported_functions = array();
		global $sb;
		foreach ($sb->publish($this->type.".plugins") as $plugin) $this->mixin($plugin);
		$this->onload();
	}

	protected function onload() {
	}

	/**
	 * register a has one relationship
	 * @param string $name the un-prefixed table name that this has one of
	 * @param string $lookup optional lookup table (table that contains the id). default is this table
	 * @param string $ref_field the column that contains the id of the related record
	 */
	protected function has_one($name, $lookup, $ref_field="") {
		if (empty($ref_field)) {
			$ref_field = $lookup;
			$lookup = $this->type;
		}
		if (!isset($this->relations[$name])) $this->relations[$name] = array();
		$this->relations[$name] = array_merge_recursive($this->relations[$name], array($lookup => array($ref_field => array("id" => array("type" => "one", "lookup" => $lookup, "ref" => $ref_field)))));
	}

	/**
	 * register a has many relationship
	 * @param string $name the un-prefixed table name that this has many of
	 * @param string $hook the column that contains the id of this table
	 * @param string $lookup optional lookup table. default is the related table
	 * @param string $ref_field optional the column that contains the id of the related record (used with lookup)
	 */
	protected function has_many($name, $hook, $lookup="", $ref_field="") {
		efault($lookup, $name);
		$key = ($ref_field) ? $ref_field : "id";
		$merge = array($lookup => array($key => array($hook => array("type" => "many", "hook" => $hook))));
		if ($lookup && $ref_field) {
			$merge[$lookup][$key][$hook]["lookup"] = $lookup;
			$merge[$lookup][$key][$hook]["ref"] = $ref_field;
		}
		if (!isset($this->relations[$name])) $this->relations[$name] = array();
		$this->relations[$name] = array_merge_recursive($this->relations[$name], $merge);
	}


	protected function store($arr, $from="auto") {
		global $sb;
		$sb->store($this->type, $arr, $from);
	}

	protected function remove($where) {
		global $sb;
		return $sb->remove($this->type, $where);
	}

	function query($args="", $froms="", $replacements=array()) {
		global $sb;
		if (is_array($froms)) {
			$replacements = $froms;
			$froms = "";
		}
		$records = $sb->query($this->type.((empty($froms)) ? "" : ",".$froms), $args, $replacements);
		$this->record_count = $sb->record_count;
		return $records;
	}

	function id_list($top, $role) {
		global $sb;
		$prefix = array($top);
		$children = $this->query("where:$role=$top");
		if (!empty($children)) foreach($children as $kid) $prefix = array_merge($prefix, $this->id_list($kid['id'], $role));
		return $prefix;
	}

	function grant() {
		global $sb;
		$_POST[$this->type]['status'] = array_sum($_POST['status']);
		$sb->grant($this->type, $_POST[$this->type]);
	}

	function filter($data) {
		return $data;
	}

	function json($args="", $froms="", $deep="auto") {
		header("Content-Type: application/json");
		$data = $this->query($args, $froms, $deep);
		$json = '[';
		foreach($data as $row) $json .= ApiFunctions::rowToJSON($row).", ";
		return rtrim($json, ", ")."]";
	}

	protected function mixin($object) {
		if (!class_exists($object)) include(BASE_DIR."/app/plugins/$object/$object.php");
		$new_import = new $object();
		$import_name = get_class($new_import);
		$import_functions = get_class_methods($new_import);
		array_push($this->imported, array($import_name, $new_import));
		foreach($import_functions as $key => $function_name) $this->imported_functions[$function_name] = &$new_import;
	}

	public function __call($method, $args=array()) {
		if(array_key_exists($method, $this->imported_functions)) {
			$args = array_merge(array($this), func_get_args());
			return call_user_func_array(array($this->imported_functions[$method], $method), $args);
		}
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}

}
?>
