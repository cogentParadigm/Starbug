<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file modules/db/src/Database.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;

use \PDO;
use \Etc;
/**
* DatabaseInterface cannonical implementation
*/
class Database implements DatabaseInterface {

	/**
	* @var PDO a PDO object
	*/
	public $pdo;
	/**
	* @var bool debug mode
	*/
	public $debug;
	/**
	* @var int holds the number of records returned by last query
	*/
	public $record_count;
	/**
	* @var int holds the id of the last inserted record
	*/
	public $insert_id;
	/**
	* @var string holds the active scope (usually 'global' or a model name)
	*/
	public $active_scope = "global";
	/**
	* @var string prefix
	*/
	public $prefix;
	/**
	* @var string database_name
	*/
	public $database_name;
	/**#@-*/
	/**
	* @var array holds records waiting to be stored
	*/
	public $queue;

	public $errors = array();

	public $operators = array(
		'=' => 1,
		'>' => 1,
		'<' => 1,
		'<=' => 1,
		'>=' => 1,
		'<>' => 1,
		'<=>' => 1,
		'!=' => 1,
		'LIKE' => 1,
		'RLIKE' => 1,
		'NOT LIKE' => 1,
		'NOT RLIKE' => 1
	);
	protected $config;
	protected $params;
	protected $models;
	protected $hooks;

	public function __construct(PDO $pdo, ModelFactoryInterface $models, HookFactoryInterface $hooks, ConfigInterface $config, $database_name) {
		$this->models = $models;
		$this->hooks = $hooks;
		$this->config = $config;
		$params = $config->get("db/".$database_name);
		$this->pdo = $pdo;
		$this->set_debug(false);
		$this->prefix = $params['prefix'];
		$this->database_name = $params['db'];
		if (defined('Etc::TIME_ZONE')) $this->exec("SET time_zone='".Etc::TIME_ZONE."'");
		$this->queue = new QueryQueue();
	}

	public function setDatabase($name, PDO $connection) {
		$this->pdo = $connection;
		$params = $this->config->get("db/".$name);
		$this->database_name = $params['db'];
		$this->prefix = $params['prefix'];
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function set_debug($debug) {
		$this->debug = (bool) $debug;
		if ($this->debug == true) $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		else $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
	}

	public function exec($statement) {
		return $this->pdo->exec($statement);
	}

	/**
	* get records or columns
	* @ingroup data
	* @param string $model the name of the model
	* @param mixed $id/$conditions the id or an array of conditions
	* @param string $column optional column name
	*/
	function get($collection, $conditions = array(), $options = array()) {
		$args = func_get_args();
		$query = $conditions = $arg = array();

		//loop through the input arguments
		foreach ($args as $idx => $a) {
			if ($idx == 0) $collection = $a; //first argument is the collection
			else if ($idx == 1) $conditions = $a; //second argument are the conditions
			else {
				$arg = $a;
				if (!empty($arg['orderby'])) $arg['sort'] = $arg['orderby']; //DEPRECATED: use sort
			}
		}
		$args = $arg;

		//apply conditions
		$query = $this->query($collection);
		if (!is_array($conditions)) $conditions = [$conditions];
		foreach ($conditions as $k => $v) {
			if (isset($this->operators[$k])) {
				$query->conditions($v, $k);
			} else {
				$col = ($k === 0) ? "id" : $k;
				//if id is compared for equality, set the limit to 1
				if ($col === "id" && !is_array($v)) $args['limit'] = 1;
				$query->condition($col, $v);
			}
		}

		if (!empty($args['sort'])) {
			foreach ($args['sort'] as $key => $direction) $query->sort($key, $direction);
		}
		if (!empty($args['limit'])) $query->limit($args['limit']);
		if (!empty($args['skip'])) $query->skip($args['skip']);


		//obtain query result
		$result = $query->execute();
		return $result;
	}

	/**
	* query the database
	* @param string $froms comma delimeted list of tables to join. 'users' or 'uris,system_tags'
	* @param string $args starbug query string for params: select, where, limit, and action/priv_type
	* @param bool $mine optional. if true, joining models will be checked for relationships and ON statements will be added
	* @return array record or records
	*/
	function query($froms, $args = array(), $replacements = array()) {
		if (!empty($args['params'])) $replacements = $args['params'];

		//create query object
		$query = new query($this, $this->models, $this->hooks, $froms);

		//call functions
		foreach ($args as $k => $v) {
			if (method_exists($query, $k)) call_user_func(array($query, $k), $v);
		}

		//set parameters
		$query->parameters = $replacements;

		//fetch results
		return ((!empty($args['limit'])) && ($args['limit'] == 1)) ? $query->execute() : $query;
	}

	/**
	* store data in the database
	* @param string $name the name of the table
	* @param string/array $fields keypairs of columns/values to be stored
	* @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
	* @return array validation errors
	*/
	function store($name, $fields = array(), $from = "auto") {
		$this->queue($name, $fields, $from, true);
		//$last = array_pop($this->to_store);
		//$this->to_store = array_merge(array($last), $this->to_store);
		$this->store_queue();
	}

	/**
	* queue data to be stored in the database pending validation of other data
	* @param string $name the name of the table
	* @param string/array $fields keypairs of columns/values to be stored
	* @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
	* @return array validation errors
	*/
	function queue($name, $fields = array(), $from = "auto", $unshift = false) {
		$query = new query($this, $this->models, $this->hooks, $name);
		foreach ($fields as $col => $value) $query->set($col, $value);

		if ($from === "auto" && !empty($fields['id'])) $from = array("id" => $fields['id']);

		if (!empty($from) && is_array($from)) {
			$query->mode("update");
			foreach ($from as $c => $v) $query->condition($c, $v);
		} else {
			$query->mode("insert");
		}

		if ($this->models->get($name)->store_on_errors) $query->store_on_errors = true;

		if ($unshift) $this->queue->unshift($query);
		else $this->queue->push($query);
	}

	/**
	* proccess the queue of data for storage
	*/
	function store_queue() {
		$this->queue->execute();
	}

	/**
	* remove from the database
	* @param string $from the name of the table
	* @param string $where the WHERE conditions on the DELETE
	*/
	function remove($from, $where) {
		if (!empty($where)) {
			$del = new query($this, $this->models, $this->hooks, $from);
			$this->record_count = $del->condition($where)->delete();
			return $this->record_count;
		}
	}

	function prefix($table) {
		if (substr($table, 0, 1) == "(") return $table;
		return $this->prefix.$table;
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

	public function error($error, $field = "global", $scope = "global") {
		$this->errors[$scope][$field][] = $error;
		$statement = $this->prepare("INSERT INTO ".$this->prefix."errors (type, field, message, action, created, modified) VALUES (?, ?, ?, ?, NOW(), NOW())");
		$statement->execute(array($scope, $field, $error, $this->models->get($scope)->action));
	}

	public function success($model, $action) {
		return (($this->models->get($model)->action == $action) && (empty($this->errors)));
	}

	public function failure($model, $action) {
		return (($this->models->get($model)->action == $action) && (!empty($this->errors)));
	}

	public function __call($method, $args) {
		if (method_exists($this->pdo, $method)) return call_user_func_array(array($this->pdo, $method), $args);
		throw new Exception('Call to undefined method/class function: ' . $method);
	}
}
?>
