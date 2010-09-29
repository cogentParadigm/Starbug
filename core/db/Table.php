<?php
// FILE: core/db/Table.php
/**
 * Table class
 * 
 * @package StarbugPHP
 * @subpackage core
 * @author Ali Gangji <ali@neonrain.com>
 * @copyright 2008-2010 Ali Gangji
 */
/**
 * This class wraps a databse table, it is the base class for database models
 * @package StarbugPHP
 * @subpackage core
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
		$this->imports = array();
		$this->imported_functions = array();
		$this->onload();
	}

	function onload() {
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
		$this->relations[$name] = array("type" => "one", "lookup" => $lookup, "ref" => $ref_field);
	}

	/**
	 * register a has many relationship
	 * @param string $name the un-prefixed table name that this has many of
	 * @param string $hook the column that contains the id of this table
	 * @param string $lookup optional lookup table. default is the related table
	 * @param string $ref_field optional the column that contains the id of the related record (used with lookup)
	 */
	protected function has_many($name, $hook, $lookup="", $ref_field="") {
		$this->relations[$name] = array("type" => "many", "hook" => $hook);
		if ($lookup && $ref_field) {
			$this->relations[$name]["lookup"] = $lookup;
			$this->relations[$name]["ref"] = $ref_field;
		}
	}


	protected function store($arr) {
		global $sb;
		$errors = $sb->store($this->type, $arr);
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
			$args[] = $this;
			return call_user_func_array(array($this->imported_functions[$method], $method), $args);
		}
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}

}
?>
