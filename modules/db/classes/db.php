<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/db/db.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * @defgroup db
 * the db class
 * @ingroup db
 */
/**
 * The db class. A simple PDO wrapper
 * @ingroup db
 */
class db {

	/**
	 * @var PDO a PDO object
	 */
	var $pdo;

	/**
	 * @var string table prefix
	 */	
	var $prefix;

	/**
	 * @var int holds the number of records returned by last query
	 */
	var $record_count;
	/**
	 * @var int holds the id of the last inserted record
	 */
	var $insert_id;
	/**
	 * @var string holds the active scope (usually 'global' or a model name)
	 */
	var $active_scope = "global";
	
	/**
	 * @var array holds instantiated models
	 */
	var $objects = array();

	/**
	 * @var bool true if in debug mode
	 */
	public $debug = false;

	/**#@-*/
	/**
	 * @var array holds records waiting to be stored
	 */
	var $to_store = array();

	public function __construct($dsn, $username=false, $password=false, $prefix="") {
		try {
			$this->pdo = new PDO($dsn, $username, $password);
			$this->set_debug(false);
			$this->prefix = $prefix;
			if (defined('Etc::TIME_ZONE')) $this->exec("SET time_zone='".Etc::TIME_ZONE."'");
		} catch (PDOException $e) { 
			die("PDO CONNECTION ERROR: " . $e->getMessage() . "\n");
		}
	}

	public function set_debug($debug) {
		$this->debug = (bool) $debug;
		if ($this->debug == true) $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		else $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
	}

  public function exec($statement) {
		try {
			return $this->pdo->exec($statement);
		} catch(PDOException $e) { 
			die("DB Exception: ".$e->getMessage()."\n");
		}
	}

	/**
	 * check if a model exists
	 * @param string $name the name of the model
	 * @return bool true if model exists, false otherwise
	 */
	function has($name) {return (($this->objects[$name]) || (file_exists(BASE_DIR."/var/models/".ucwords($name)."Model.php")));}	

	/**
	 * get a model by name
	 * @param string $name the name of the model, such as 'users'
	 * @return the instantiated model
	 */
	function model($name) {
		$class = ucwords($name);
		if (!isset($this->objects[$name])) {
			if (file_exists(BASE_DIR."/var/models/".$class."Model.php")) {
				//include the base model
				include(BASE_DIR."/var/models/".$class."Model.php");
				$last = $class."Model";
				
				//get additional models
				$models = array_reverse(locate("$class.php", "models"));
				$count = count($models);
				$search = "class $class {";
				
				//loop through found models
				for ($i = 0; $i < $count; $i++) {
					//get file contents
					$contents = file_get_contents($models[$i]);
					//make class name unique and extend the previous class
					$class = str_replace(array(BASE_DIR.'/', '/'), array('', '_'), reset(explode('/models/', $models[$i])))."_$class";
					$replace = "class $class extends $last {";
					//replace and eval
					eval('?>'.str_replace($search, $replace, $contents));
					//set $last for the next round
					$last = $class;
				}
				
				//return the base model if no others
				if ($count == 0) $class .= "Model";
				
			} else $class = "Table"; //return the base table if the model does not exist
			
			//instantiate save the object
			$this->objects[$name] = new $class($this, $name);
		}
		
		//return the saved object
		return $this->objects[$name];
	}

	/**
	 * query the database
	 * @param string $froms comma delimeted list of tables to join. 'users' or 'uris,system_tags'
	 * @param string $args starbug query string for params: select, where, limit, and action/priv_type
	 * @param bool $mine optional. if true, joining models will be checked for relationships and ON statements will be added
	 * @return array record or records
	 */
	function query($froms, $args="", $replacements=array()) {
		$froms = explode(",", $froms);
		$first = array_shift($froms);
		$args = star($args);
		$schema = (function_exists("schema")) ? schema($first) : array();
		if (!isset($args['search'])) unset($schema['search']);
		else if (empty($args['search'])) unset($args['search']);
		foreach ($schema as $k => $v) if (!isset($args[$k]) && is_string($v)) $args[$k] = $v;
		efault($args['select'], "*");
		efault($args['join'], "INNER");
		efault($args['mine'], true);
		$from = "`".$this->prefix.$first."` AS `".$first."`";
		if (!$args['mine']) foreach ($froms as $f) $from .= " $args[join] JOIN `".$this->prefix.$f."` AS `".$f."`";
		else if (!empty($froms)) {
			$relations = $this->model($first)->relations;
			$last = "";
			$joined = array();
			foreach ($froms as $f) {
				$f = explode(" via ", $f);
				if (1 == count($f)) {
					if (isset($relations[$f[0]][$last])) $rel = reset(reset($relations[$f[0]][$last]));
					else if (isset($relations[$f[0]][$first])) $rel = reset(reset($relations[$f[0]][$first]));
					else if (isset($relations[$f[0]][$f[0]])) $rel = reset(reset($relations[$f[0]][$f[0]]));
					else if (isset($relations[$f[0]])) $rel = reset(reset(reset($relations[$f[0]])));
					else if (!empty($last)) {
						$set = $this->model($last)->relations;
						if (isset($set[$f[0]])) $rel = reset(reset(reset($set[$f[0]])));
					}
				} else {
					$parts = explode(" ", $f[1]);
					$f[1] = $parts[0];
					$rel = $relations[$f[0]][$f[1]];
					if (1 == count($parts)) $rel = reset(reset($rel));
					else if (2 == count($parts)) {
						$rel = reset($rel);
						$rel = $rel[$parts[1]];
					} else $rel = $rel[$parts[1]][$parts[2]];
				}
				$last = $f[0];
				$lookup = $f[1];
				$f = $f[0];
				$namejoin = " $args[join] JOIN `".$this->prefix.$f."` AS `$f`";
				$joined[] = $f;
				if (empty($rel)) $from .= $namejoin;
				else {
					$namejoin .= " ON ";
					if ($rel['type'] == "one") $from .= $namejoin."$rel[lookup].$rel[ref]=".(($rel['lookup'] == $f) ? $first : $f).".id";
					else if ($rel['type'] == "many") {
						if ($rel['lookup']) {
							if (false === array_search($rel['lookup'], $joined)) {
								$from .= " $args[join] JOIN ".P($rel['lookup'])." AS $rel[lookup] ON ".$first.".id=$rel[lookup].$rel[hook]";
								$joined[] = $rel['lookup'];
							}
							$from .= $namejoin." $rel[lookup].$rel[ref]=$f.id";
						} else {
							$from .= $namejoin.$first.".id=$f.$rel[hook]";
						}
					}
				}
			}
		}
		foreach ($args as $k => $v) {
			foreach (locate("query/$k.php", "filters") as $filter) include($filter);
		}
		if (!empty($args['where'])) $args['where'] = " WHERE ".$args['where'];
		$groupby = (!(empty($args['groupby']))) ? " GROUP BY $args[groupby]" : "";
		$having = (!(empty($args['having']))) ? " HAVING $args[having]" : "";
		$limit = (!(empty($args['limit']))) ? " LIMIT $args[limit]" : "";
		$order = (!(empty($args['orderby']))) ? " ORDER BY $args[orderby]" : "";
		$sql = " FROM $from$args[where]$order$limit";
		if (isset($args['echo'])) echo "SELECT $args[select] ".$sql;
		try {
			$res = $this->pdo->prepare("SELECT COUNT(*)".$sql);
			$res->execute($replacements);
			$this->record_count = $res->fetchColumn();
			$records = $this->pdo->prepare("SELECT $args[select] FROM $from$args[where]$groupby$having$order$limit");
			$records->execute($replacements);
		} catch(PDOException $e) {
			die("DB Exception: ".$e->getMessage());
		}
		$rows = $records->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $idx => $row) foreach ($row as $col => $value) $rows[$idx][$col] = stripslashes($value);
		return ((!empty($args['limit'])) && ($args['limit'] == 1)) ? $rows[0] : $rows;
	}

	/**
	 * store data in the database
	 * @param string $name the name of the table
	 * @param string/array $fields keypairs of columns/values to be stored
	 * @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
	 * @return array validation errors
	 */
	function store($name, $fields, $from="auto") {
		$this->queue($name, $fields, $from);
		$last = array_pop($this->to_store);
		$this->to_store = array_merge(array($last), $this->to_store);
		$this->store_queue();
	}

	/**
	 * queue data to be stored in the database pending validation of other data
	 * @param string $name the name of the table
	 * @param string/array $fields keypairs of columns/values to be stored
	 * @param string/array $from optional. keypairs of columns/values to be used in an UPDATE query as the WHERE clause
	 * @return array validation errors
	 */
	function queue($name, $fields, $from="auto") {
		$oldscope = error_scope();
		error_scope($name);
		$thefilters = $this->model($name)->filters;
		$errors = $byfilter = array();
		$storing = false;
		if (!is_array($fields)) $fields = star($fields);
		if ($from == "auto") {
			if (!empty($fields['id'])) $from = array("id" => $fields['id']);
		} else if ((false !== $from) && (!is_array($from))) $from = star($from);
		foreach ($fields as $col => $value) {
			$errors[$col] = array();
			$fields[$col] = trim($fields[$col]);
			$filters = star($thefilters[$col]);
			foreach($filters as $filter => $args) $byfilter[$filter][$col] = $args;
			if ($value === "") $errors[$col]["required"] = "This field is required.";
		}
		foreach($byfilter as $filter => $args) {
			$on_store = false;
			foreach (locate("store/$filter.php", "filters") as $f) include($f);
			if (!$on_store) unset($byfilter[$filter]);
		}
		foreach($errors as $col => $err) foreach ($err as $e => $m) error($m, $col);
		$this->to_store[] = array("model" => $name, "fields" => $fields, "from" => $from, "filters" => $byfilter);
		error_scope($oldscope);
	}

	/**
	 * proccess the queue of data for storage
	 */
	function store_queue() {
		$oldscope = error_scope();
		foreach ($this->to_store as $store) {
			$storing = true;
			$inserting = $updating = false;
			$name = $store['model'];
			error_scope($name);
			$fields = $store['fields'];
			$from = $store["from"];
			$filters = $store["filters"];
			$errors = $prize = array();
			if (is_array($from)) {
				if (!empty($fields['id']) && !empty($from['id'])) unset($fields['id']);
				$updating = true;
			} else $inserting = true;
			foreach ($filters as $filter => $args) {
				foreach (locate("store/$filter.php", "filters") as $f) include($f);
			}
			foreach($errors as $col => $err) foreach ($err as $e => $m) error($m, $col);
			if (!errors()) { //no errors
				$pre_store_time = date("Y-m-d H:i:s");
				$fields['modified'] = date("Y-m-d H:i:s");
				if ($updating) { //updating existing record
					$setstr = $wherestr = "";
					foreach($fields as $col => $value) {
						if ($value == "NULL") $s = "NULL";
						else {
							$prize[] = $value;
							$s = "?";
						}
						if(empty($setstr)) $setstr = $col."= $s";
						else $setstr .= ", ".$col."= $s";
					}
					foreach($from as $c => $v) {
						$prize[] = $v;
						if (empty($wherestr)) $wherestr = $c." = ?";
						else $wherestr .= " && ".$c." = ?";
					}
					$stmt = $this->pdo->prepare("UPDATE ".$this->prefix.$name." SET ".$setstr." WHERE ".$wherestr);
					$this->record_count = $stmt->execute($prize);
				} else { //creating new record
					$fields['created'] = date("Y-m-d H:i:s");
					if (!isset($fields['owner'])) $fields['owner'] = ($_SESSION[P('id')] > 0) ? $_SESSION[P('id')] : 1;
					$keys = ""; $values = "";
					foreach($fields as $col => $value) {
						if ($value == "NULL") $s = "NULL";
						else if ($value == "now()") $s = "now()";
						else {
							$prize[] = $value;
							$s = "?";
						}
						if(empty($keys)) $keys = $col;
						else $keys .= ", ".$col;
						if(empty($values)) $values = "$s";
						else $values .= ", $s";
					}
					$stmt = $this->pdo->prepare("INSERT INTO ".$this->prefix.$name." (".$keys.") VALUES (".$values.")");
					$this->record_count = $stmt->execute($prize);
					$this->insert_id = $this->pdo->lastInsertId();
					$this->model($name)->insert_id = $this->insert_id;
				}
				//NOTIFY CLIENTS
				if (defined("Etc::API_NOTIFIER")) {
					$this->import("core/ApiRequest");
					$curl = get_curl();
					$curl->follow_redirects = false;
					$subs = $curl->get(Etc::API_NOTIFIER);
					$subs = json_decode($subs->body, true);
					if (!is_array($subs)) $subs = array();
					$result = array();
					foreach ($subs as $idx => $call) {
						list($models, $query) = explode("  ", $call, 2);
						$request = new ApiRequest($models.".json", $query."  log:log.created>='$pre_store_time'  select:log.*", false);
						if (empty($request->result)) $result[] = '"'.$idx.'":[]';
						else $result[] = '"'.$idx.'": '.$request->result;
					}
					$postdata = array("response" => "{ ".implode(", ", $result)." }");
					$curl->post(Etc::API_NOTIFIER, $postdata);
				}
			}
		}
		$this->to_store = array();
		error_scope($oldscope);
	}

	/**
	 * remove from the database
	 * @param string $from the name of the table
	 * @param string $where the WHERE conditions on the DELETE
	 */
	function remove($from, $where) {
		if (!empty($where)) {
			try {
				$del = $this->pdo->prepare("DELETE FROM ".$this->prefix.$from." WHERE ".$where);
				$del->execute();
				$this->record_count = $del->rowCount();
				return array();
			} catch(PDOException $e) {
				error($e->getMessage(), "global", "DB");
				die("DB Exception: ".$e->getMessage());
			}
		}
	}

	public function __call($method, $args) {
		if(method_exists($this->pdo, $method)) return call_user_func_array(array($this->pdo, $method), $args);
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}

}
?>
