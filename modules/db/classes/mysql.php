<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file modules/db/class/mysql.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * @defgroup mysql
 * the db class
 * @ingroup db
 */
/**
 * The mysql class. A simple PDO wrapper
 * @ingroup mysql
 */
class mysql extends db {

	/**
	 * @var PDO a PDO object
	 */
	var $pdo;
	/**
	 * @var bool debug mode
	 */
	var $debug;
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
	 * @var string prefix
	 */
	var $prefix;
	/**#@-*/
	/**
	 * @var array holds records waiting to be stored
	 */
	var $to_store = array();

	public function __construct($params) {
		try {
			$this->pdo = new PDO('mysql:host='.$params['host'].';dbname='.$params['db'], $params['username'], $params['password']);
			$this->set_debug(false);
			$this->prefix = $params['prefix'];
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
		return $this->pdo->exec($statement);
	}

	/**
	 * get records or columns
	 * @ingroup data
	 * @param string $model the name of the model
	 * @param mixed $id/$conditions the id or an array of conditions
	 * @param string $column optional column name
	 */
	function get() {
		$args = func_get_args();
		$query = $conditions = $replacements = array();
		
		//loop through the input arguments
		foreach ($args as $idx => $arg) {
				if ($idx == 0) $collection = $arg; //first argument is the collection
				else if ($idx == 1) $conditions = star($arg); //second argument are the conditions
				else {
					$arg = star($arg);
					if (!empty($arg['orderby'])) $arg['sort'] = $arg['orderby']; //DEPRECATED: use sort
					if (!empty($arg['sort'])) {
						foreach ($arg['sort'] as $key => $direction) $query['orderby'][] = $key." ".(($direction > 0) ? "ASC" : "DESC");
						$query['orderby'] = implode(", ", $query['orderby']);
					}
					if (!empty($arg['limit'])) $query['limit'] = $arg['limit'];
					if (!empty($arg['skip'])) $query['limit'] = $arg['skip'].", ".$query['limit'];
				}
		}
		
		//if there are any conditions, convert them to string expressions
		$conditions = star($conditions);
		foreach ($conditions as $k => $v) {
			$col = ($k === 0) ? "id" : $k;
			if (!is_array($v)) $v = array($v, '=');
			$conditions[$k] = $col." ".$v[1]." ?";
			$replacements[] = $v[0];
			//if id is compared for equality, set the limit to 1
			if ($col == "id" && $v[1] == "=") $query['limit'] = 1;
		}
		if (!empty($conditions)) $query['where'] = implode(" && ", $conditions);
		
		//obtain query result
		$result = $this->query($collection, $query, $replacements);
		return $result;
	}

	/**
	 * query the database
	 * @param string $froms comma delimeted list of tables to join. 'users' or 'uris,system_tags'
	 * @param string $args starbug query string for params: select, where, limit, and action/priv_type
	 * @param bool $mine optional. if true, joining models will be checked for relationships and ON statements will be added
	 * @return array record or records
	 */
	function query($froms, $args="", $replacements=array()) {
		$args = star($args);
		if (!empty($args['params'])) $replacements = $args['params'];
		
		//list of tables
		$froms = explode(",", $froms);
		
		//first table
		$first = array_shift($froms);
		
		//schema
		$schema = (function_exists("schema")) ? schema($first) : array();
		
		//set defaults from schema, only use search if $args[search] is set and empty
		if (!isset($args['search'])) unset($schema['search']);
		else if (empty($args['search'])) $args['search'] = $schema['search'];
		foreach ($schema as $k => $v) if (!isset($args[$k]) && is_string($v)) $args[$k] = $v;

		//after schema defaults have been set, set global defaults
		efault($args['select'], "*");
		efault($args['join'], "INNER");
		efault($args['mine'], true);
		
		//build FROM clause with joins
		$from = "`".$this->prefix.$first."` AS `".$first."`";
		if (!$args['mine']) foreach ($froms as $f) $from .= " $args[join] JOIN `".$this->prefix.$f."` AS `".$f."`";
		else if (!empty($froms)) {
			
			//build JOINs
			$relations = db::model($first)->relations;
			$last = "";
			$joined = array();
			
			//loop through joining models to determine relations
			foreach ($froms as $f) {

				//if the relationship is ambiguous, use via to be clear
				$f = explode(" via ", $f);
				
				if (1 == count($f)) { //determine best relation
				
					if (isset($relations[$f[0]][$last])) $rel = reset(reset($relations[$f[0]][$last]));
					else if (isset($relations[$f[0]][$first])) $rel = reset(reset($relations[$f[0]][$first]));
					else if (isset($relations[$f[0]][$f[0]])) $rel = reset(reset($relations[$f[0]][$f[0]]));
					else if (isset($relations[$f[0]])) $rel = reset(reset(reset($relations[$f[0]])));
					else if (!empty($last)) {
						$set = db::model($last)->relations;
						if (isset($set[$f[0]])) $rel = reset(reset(reset($set[$f[0]])));
					}
					
				} else { //use via

					$parts = explode(" ", $f[1]);
					$f[1] = $parts[0];
					$rel = $relations[$f[0]][$f[1]];
					if (1 == count($parts)) $rel = reset(reset($rel));
					else if (2 == count($parts)) {
						$rel = reset($rel);
						$rel = $rel[$parts[1]];
					} else $rel = $rel[$parts[1]][$parts[2]];

				}
				
				//convert relationship to JOIN clause
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

			} //end loop for JOINs
		} //end FROM clause
		$args['from'] = $from;
		
		//run filters
		foreach ($args as $k => $v) {
			foreach (locate("query/$k.php", "filters") as $filter) include($filter);
		}
		
		//prepare query parts
		$select = "SELECT $args[select]";
		$from = " FROM $args[from]";
		$where = (!(empty($args['where']))) ? " WHERE $args[where]" : "";
		$groupby = (!(empty($args['groupby']))) ? " GROUP BY $args[groupby]" : "";
		$having = (!(empty($args['having']))) ? " HAVING $args[having]" : "";
		$limit = (!(empty($args['limit']))) ? " LIMIT $args[limit]" : "";
		$order = (!(empty($args['orderby']))) ? " ORDER BY $args[orderby]" : "";
		$sql = "$select$from$where$groupby$having$order$limit";

		if (isset($args['echo'])) {
			echo $sql;
			exit();
		}

		$records = $this->pdo->prepare($sql);
		$records->execute($replacements);
		$rows = $records->fetchAll(PDO::FETCH_ASSOC);
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
		$thefilters = db::model($name)->filters;
		$errors = $byfilter = array();
		$storing = false;
		if (!is_array($fields)) $fields = star($fields);
		if ($from == "auto") {
			if (!empty($fields['id'])) $from = array("id" => $fields['id']);
		} else if ((false !== $from) && (!is_array($from))) $from = star($from);
		foreach ($fields as $col => $value) {
			$errors[$col] = array();
			$filters = star($thefilters[$col]);
			foreach($filters as $filter => $args) $byfilter[$filter][$col] = $args;
			if ($value === "") $errors[$col]["required"] = "This field is required.";
		}
		foreach($byfilter as $filter => $args) {
			$on_store = $after_store = false;
			foreach (locate("store/$filter.php", "filters") as $f) include($f);
			if (!$on_store && !$after_store) unset($byfilter[$filter]);
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
				$after_store = false;
				foreach (locate("store/$filter.php", "filters") as $f) include($f);
				if (!$after_store) unset($filters[$filter]);
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
						if(empty($setstr)) $setstr = "`".$col."`= $s";
						else $setstr .= ", `".$col."`= $s";
					}
					foreach($from as $c => $v) {
						$prize[] = $v;
						if (empty($wherestr)) $wherestr = "`".$c."` = ?";
						else $wherestr .= " && `".$c."` = ?";
					}
					$stmt = $this->pdo->prepare("UPDATE ".$this->prefix.$name." SET ".$setstr." WHERE ".$wherestr);
					$this->record_count = $stmt->execute($prize);
				} else { //creating new record
					$fields['created'] = date("Y-m-d H:i:s");
					if (!isset($fields['owner'])) $fields['owner'] = (logged_in()) ? sb()->user['id'] : 1;
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
					db::model($name)->insert_id = $this->insert_id;
					$fields["id"] = $this->insert_id;
					foreach ($filters as $filter => $args) {
						foreach (locate("store/$filter.php", "filters") as $f) include($f);
					}
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
			$del = $this->pdo->prepare("DELETE FROM ".$this->prefix.$from." WHERE ".$where);
			$del->execute();
			$this->record_count = $del->rowCount();
			return array();
		}
	}
	
	/**
	 * build a search clause to be put into a WHERE clause
	 * @param string $text a natural language search string which can include operators 'and' and 'or' and quotes for exact matches
	 * @param array $fields a list of columns to search on
	 * @return string SQL WHERE component
	 * examples,
	 * 
	 * search string: 'beef and broccoli'
	 * fields: array('name', 'description')
	 * return: ((name LIKE '%beef%' OR description LIKE '%beef%') and (name LIKE '%broccoli%' OR description LIKE '%broccoli%'))
	 */
	function search_clause($text, $fields) {
		$text = strtolower(trim(str_replace("\\\"","&quot;",$text)));
		//tokenize the text
		$output = array();
		$output2 = array();
		$arr = explode("&quot;",$text);
		for ($i=0;$i<count($arr);$i++){
			if ($i%2==0) $output=array_merge($output,explode(" ",$arr[$i]));
			else $output[] = $arr[$i];
		}
		foreach($output as $token) if (trim($token)!="") $words[]=$token;
		//generate condition string
		$conditions = "(";
		for($word=0;$word<count($words);$word++) {
			$w = $words[$word];
			if ($w!="") {
				if ($w!="and" && $w!="or") {
					$conditions .= "(";
					for($field=0;$field<count($fields);$field++) {
						$conditions .= $fields[$field]." LIKE '%".$w."%'";
						if ($field<(count($fields)-1)) {
							$conditions .= " OR ";
						} else {
							$conditions .= ")";
						}
					}
					if ($word<(count($words)-1)) {
						if ($words[$word+1]=="and" || $words[$word+1]=="or") {
							$conditions .= " ".$words[$word+1]." ";
						} else {
							$conditions .= " AND ";
						}
					}
				}
			}
		}
		$conditions .= ")";
		return $conditions;
	}

	public function __call($method, $args) {
		if(method_exists($this->pdo, $method)) return call_user_func_array(array($this->pdo, $method), $args);
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}

}
?>
