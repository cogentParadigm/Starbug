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
	 * @var db the db object
	 */
	public $db;
	/**
	 * @var string The unprefixed table name
	 */
	public $type;
	/**
	 * @var array The hooks that apply to each column
	 */
	public $hooks = array();
	/**
	 * @var array The relationships to other tables
	 */
	public $relations = array();
	/**
	 * @var int The number of records returned by the last query
	 */
	public $record_count;
	/**
	 * @var int The id of the last record inserted
	 */
	public $insert_id;
	/**
	 * @var array The mixed-in objects which hold the imported functions
	 */
	public $store_on_errors = false;

	protected $models;
	public $errors = array();
	public $action = false;

	/**
	 * Table constructor
	 * @param string $type the un-prefixed table name
	 * @param array $filters the column filters
	 */
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->models = $models;
		//$this->logger = $loggers->get(get_class($this));
		$this->init();
	}

	public function errors($key = "", $values = false) {
		if (is_bool($key)) {
			$values = $key;
			$key = "";
		}
		$parts = explode(".", $key);
		$errors = $this->errors;
		if (!empty($key)) foreach ($parts as $p) $errors = $errors[$p];
		if ($values) return $errors;
		else return (!empty($errors));
	}

	public function error($error, $field = "global") {
		$this->errors[$field][] = $error;
		//$this->logger->info("{model}::{action} - {field}:{message}", array("model" => $model, "action" => $this->request->data['action'][$model], "field" => $field, "message" => $error));
	}

	public function success($action) {
		return (($this->action == $action) && (empty($this->errors)));
	}

	public function failure($action) {
		return (($this->action == $action) && (!empty($this->errors)));
	}

	/**
	* run a model action if permitted
	* @param string $key the model name
	* @param string $value the function name
	*/
	protected function post($action, $data = array()) {
		$this->action = $action;
		if (isset($data['id'])) {
			$permits = $this->db->query($this->type)->action($action)->condition($this->type.".id", $data['id'])->one();
		} else {
			$permits = $this->db->query("permits")->action($action, $this->type)->one();
		}
		if ($permits) {
			$this->$action($data);
			return true;
		} else {
			$this->error("Access Denied");
			return false;
		}
	}

	protected function init() {
	}

	/**
	 * register a has one relationship
	 * @param string $name the un-prefixed table name that this has one of
	 * @param string $lookup optional lookup table (table that contains the id). default is this table
	 * @param string $ref_field the column that contains the id of the related record
	 */
	protected function has_one($name, $ref_field, $hook = "id") {
		$lookup = $this->type;
		if (!isset($this->relations[$name])) $this->relations[$name] = array();
		$this->relations[$name] = array_merge_recursive($this->relations[$name], array($lookup => array($ref_field => array("id" => array("type" => "one", "lookup" => $lookup, "ref" => $ref_field, "hook" => $hook)))));
	}

	/**
	 * register a has many relationship
	 * @param string $name the un-prefixed table name that this has many of
	 * @param string $hook the column that contains the id of this table
	 * @param string $lookup optional lookup table. default is the related table
	 * @param string $ref_field optional the column that contains the id of the related record (used with lookup)
	 */
	protected function has_many($name, $hook, $lookup = "", $ref_field = "") {
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

	/**
	 * store a record to the db
	 * @see db::store
	 */
	protected function store($record, $from = "auto") {
		$this->db->store($this->type, $record, $from);
	}

	/**
	 * remove a record from the db
	 * @see db::remove
	 */
	protected function remove($where) {
		return $this->db->remove($this->type, $where);
	}

	/**
	 * get records from the db
	 * @see db::get
	 */
	function get() {
		$args = func_get_args();
		array_unshift($args, $this->type);
		return call_user_func_array(array($this->db, "get"), $args);
	}

	/**
	 * get records from the db
	 * @see db::query
	 */
	function query($args = "", $froms = "", $replacements = array()) {
		if (is_array($froms)) {
			$replacements = $froms;
			$froms = "";
		}
		$records = $this->db->query($this->type.((empty($froms)) ? "" : ",".$froms), $args, $replacements);
		$this->record_count = $this->db->record_count;
		return $records;
	}

	function filter($data, $action = "") {
		if (!empty($this->base)) {
			$data = $this->models->get($this->base)->filter($data, $action);
		}
		return $data;
	}


	function build_display($display) {
		$display->add("id");
	}

	function query_filters($action, $query, &$ops) {
		if (!empty($this->base)) {
			sb($this->base)->query_filters($action, $query, $ops);
		} else {
			if (!empty($ops['keywords'])) $query->search($ops['keywords']);
		}
		return $query;
	}
}
?>
