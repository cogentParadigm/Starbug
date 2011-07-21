<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/sb.php
 * The global sb object. provides data, errors, import/provide, load and pub/sub. The backbone of Starbug
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
/**
 * The sb class. Provides data, errors, import/provide, pub/sub, and load to the rest of the application. The core component of StarbugPHP.
 * @ingroup core
 */
class sb {
	/**#@+
	* @access public
	*/
	/**
	 * @var db The db class is a PDO wrapper
	 */
	var $db;
	/**
	 * @var int holds the number of records returned by last query
	 */
	var $record_count;
	/**
	 * @var int holds the id of the last inserted record
	 */
	var $insert_id;
	/**
	 * @var array holds the utils that have been provided
	 */
	var $provided = array();
	/**
	 * @var array holds instantiated models
	 */
	var $objects = array();
	/**
	 * @var array holds validation errors
	 */
	var $errors = array();
	/**
	 * @var array holds alerts
	 */
	var $alerts = array();
	/**
	 * @var array holds mixed in objects
	 */
	var $imported = array();
	/**
	 * @var array holds function names of mixed in objects
	 */
	var $imported_functions = array();
	/**#@-*/
	/**
	 * @var array holds records waiting to be stored
	 */
	var $to_store = array();

	/**
	 * constructor. connects to db and sets default $_SESSION values
	 */
	function __construct() {
		$this->db = new db('mysql:host='.Etc::DB_HOST.';dbname='.Etc::DB_NAME, Etc::DB_USERNAME, Etc::DB_PASSWORD);
		$this->db->set_debug(true);
		if (!isset($_SESSION[P('id')])) {
			$_SESSION[P('id')] = $_SESSION[P('memberships')] = 0;
			$_SESSION[P('user')] = array();
		}
		$this->publish("init");
	}

	/**
	 * since you can't subscribe the handle 'include', use sb::load
	 * @param string $what 'jsforms' works for either 'jsforms.php' or 'jsforms/jsforms.php'
	 */
	function load($what) {
		global $request;
		if (file_exists($what.".php")) include($what.".php");
		else {
			$token = split("/", $what); $token = $what."/".end($token).".php";
			if (file_exists($token)) include($token);
		}
	}

	/**
	 * publish a topic to any subscribers
	 * @param string $topic the topic name you would like to publish
	 * @param mixed $args any additional parameters will be passed in an array to the subscriber
	 */
	function publish($topic, $args=null) {
		global $request;
		$return = array();
		$args = func_get_args();
		if (false !== strpos($topic, ".")) {
			list($tags, $topic) = explode(".", $topic);
			$tags = array(array("tag" => $tags));
		} else $tags = (isset($request->tags)) ? $request->tags : array(array("tag" => "global"));
		foreach ($tags as $tag) {
			$subscriptions = (file_exists(BASE_DIR."/etc/hooks/$tag[tag].$topic")) ? unserialize(file_get_contents(BASE_DIR."/etc/hooks/$tag[tag].$topic")) : array();
			foreach ($subscriptions as $priority) {
				foreach($priority as $handle) {
					if (false !== strpos($handle['handle'], "::")) $handle['handle'] = explode("::", $handle['handle']);
					$return[] = call_user_func($handle['handle'], $handle['args'], $args);
				}
			}
		}
		return $return;
	}

	/**
	 * import function. only imports once when used with provide
	 * @param string $loc path of file to import without '.php' at the end
	 */
	function import($loc) {global $sb; $args = func_get_args(); foreach($args as $l) if (!$this->provided[$l]) include(BASE_DIR."/".$l.".php");}

	/**
	 * when imported use provide to prevent further imports from attempting to include it again
	 * @param string $loc the imported location. if i were to use $sb->import("util/form"), util/form.php would have $sb->provide("util/form") at the top
	 */
	function provide($loc) {$this->provided[$loc] = true;}

	/**
	 * get a model by name
	 * @param string $name the name of the model, such as 'users'
	 * @return the instantiated model
	 */
	function get($name) {
		$obj = ucwords($name);
		if (!isset($this->objects[$name])) {
			include(BASE_DIR."/var/models/".$obj."Model.php");
			if (file_exists(BASE_DIR."/app/models/$obj.php")) include(BASE_DIR."/app/models/$obj.php");
			else $obj .= "Model";
			$this->objects[$name] = new $obj($name);
		}
		return $this->objects[$name];
	}

	/**
	 * check if a model exists
	 * @param string $name the name of the model
	 * @return bool true if model exists, false otherwise
	 */
	function has($name) {return (($this->objects[$name]) || (file_exists(BASE_DIR."/var/models/".ucwords($name)."Model.php")));}

	/**
	 * query the database
	 * @param string $froms comma delimeted list of tables to join. 'users' or 'uris,system_tags'
	 * @param string $args starbug query string for params: select, where, limit, and action/priv_type
	 * @param bool $mine optional. if true, joining models will be checked for relationships and ON statements will be added
	 * @return array record or records
	 */
	function query($froms, $args="", $mine=true) {
		$froms = explode(",", $froms);
		$first = array_shift($froms);
		$args = starr::star($args);
		efault($args['select'], "*");
		efault($args['join'], "INNER");
		$from = "`".P($first)."` AS `".$first."`";
		if (!$mine) foreach ($froms as $f) $from .= " $args[join] JOIN `".P($f)."` AS `".$f."`";
		else if (!empty($froms)) {
			$relations = $this->get($first)->relations;
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
						$set = $this->get($last)->relations;
						if (isset($set[$f[0]])) $rel = reset(reset(reset($set[$f[0]])));
					}
				} else {
					$parts = explode(" ", $f[1]);
					$f[1] = $parts[0];
					$rel = $relations[$f[0]][$f[1]];
					if (1 == count($parts)) $rel = reset(reset($rel));
					else if (2 == count($parts)) $rel = reset($rel[$parts[1]]);
					else $rel = $rel[$parts[1]][$parts[2]];
				}
				$last = $f[0];
				$lookup = $f[1];
				$f = $f[0];
				$namejoin = " $args[join] JOIN `".P($f)."` AS `$f`";
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
		foreach ($args as $k => $v) if (file_exists(BASE_DIR."/core/app/filters/query/$k.php")) include(BASE_DIR."/core/app/filters/query/$k.php");
		if (!empty($args['where'])) $args['where'] = " WHERE ".$args['where'];
		$groupby = (!(empty($args['groupby']))) ? " LIMIT $args[groupby]" : "";
		$having = (!(empty($args['having']))) ? " LIMIT $args[having]" : "";
		$limit = (!(empty($args['limit']))) ? " LIMIT $args[limit]" : "";
		$order = (!(empty($args['orderby']))) ? " ORDER BY $args[orderby]" : "";
		$sql = " FROM $from$args[where]$order$limit";
		if (isset($args['echo'])) echo "SELECT $args[select] ".$sql;
		try {
			$res = $this->db->query("SELECT COUNT(*)".$sql);
			$this->record_count = $res->fetchColumn();
			$records = $this->db->prepare("SELECT $args[select] FROM $from$args[where]$groupby$having$order$limit");
			$records->execute();
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
		$thefilters = ($this->has($name)) ? $this->get($name)->filters : array();
		$errors = $byfilter = array();
		$storing = false;
		if (!is_array($fields)) $fields = starr::star($fields);
		foreach ($fields as $col => $value) {
			$errors[$col] = array();
			$fields[$col] = trim($fields[$col]);
			$filters = starr::star($thefilters[$col]);
			foreach($filters as $filter => $args) $byfilter[$filter][$col] = $args;
			if ($value === "") $errors[$col]["required"] = "This field is required.";
		}
		foreach($byfilter as $filter => $args) {
			$on_store = false;
			include(BASE_DIR."/core/app/filters/store/$filter.php");
			if (!$on_store) unset($byfilter[$filter]);
		}
		foreach($errors as $col => $err) if (empty($err)) unset($errors[$col]);
		if (!empty($errors)) $this->errors = array_merge_recursive($this->errors, array($name => $errors));
		$this->to_store[] = array("model" => $name, "fields" => $fields, "from" => $from, "filters" => $byfilter);
	}

	/**
	 * proccess the queue of data for storage
	 */
	function store_queue() {
		foreach ($this->to_store as $store) {
			$storing = true;
			$inserting = $updating = false;
			$name = $store['model'];
			$fields = $store['fields'];
			$from = $store["from"];
			$filters = $store["filters"];
			$errors = $prize = array();
			if ($from == "auto") {
				if (!empty($fields['id'])) {
					$from = array("id" => $fields['id']);
					unset($fields['id']);
				}
			} else if ((false !== $from) && (!is_array($from))) $from = starr::star($from);
			if (is_array($from)) $updating = true; else $inserting = true;
			foreach ($filters as $filter => $args) include(BASE_DIR."/core/app/filters/store/$filter.php");
			foreach($errors as $col => $err) if (empty($err)) unset($errors[$col]);
			if (!empty($errors)) $this->errors = array_merge_recursive($this->errors, array($name => $errors));
			if (empty($this->errors)) { //no errors
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
					$stmt = $this->db->prepare("UPDATE ".P($name)." SET ".$setstr." WHERE ".$wherestr);
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
					$stmt = $this->db->prepare("INSERT INTO ".P($name)." (".$keys.") VALUES (".$values.")");
					$this->record_count = $stmt->execute($prize);
					$this->insert_id = $this->db->lastInsertId();
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
						$request = new ApiRequest($models.".json", $query."  log:log.created>='$pre_store_time'  select:log.*");
						if (empty($request->result)) $result[] = '"'.$idx.'":[]';
						else $result[] = '"'.$idx.'": '.$request->result;
					}
					$postdata = array("response" => "{ ".implode(", ", $result)." }");
					$curl->post(Etc::API_NOTIFIER, $postdata);
				}
			}
		}
		$this->to_store = array();
	}

	/**
	 * remove from the database
	 * @param string $from the name of the table
	 * @param string $where the WHERE conditions on the DELETE
	 */
	function remove($from, $where) {
		if (!empty($where)) {
			try {
				$del = $this->db->prepare("DELETE FROM ".P($from)." WHERE ".$where);
				$del->execute();
				$this->record_count = $del->rowCount();
				return array();
			} catch(PDOException $e) {
				die("DB Exception: ".$e->getMessage());
			}
		}
	}

	/**
	 * run a model action if permitted
	 * @param string $key the model name
	 * @param string $value the function name
	 */
	function post_act($key, $value) {
		if ($object = $this->get($key)) {
			$permits = isset($_POST[$key]['id']) ? $this->query($key, "action:$value  where:$key.id='".$_POST[$key]['id']."'") : $this->query($key, "action:$value  priv_type:table");
			if (($this->record_count > 0) || ($_SESSION[P('memberships')] & 1)) $errors = $object->$value();
			else $errors = array("global" => array("forbidden" => "You do not have sufficient permission to complete your request."));
			if (!empty($errors)) $this->errors = array_merge_recursive($this->errors, array($key => $errors));
			if (!empty($this->errors[$key])) {
				global $request;
				if ($request->format != "json") $request->return_path();
			}
		}
	}

	/**
	 * check $_POST['action'] for posted actions and run them through post_act
	 */
	function check_post() {if (!empty($_POST['action'])) foreach($_POST['action'] as $key => $val) $this->post_act($key, $val);}

	/**
	 * grant permissions
	 * @param string $table the table on which to apply the permit
	 * @param array $permit the permit record
	 * @return errors array
	 */
	function grant($table, $permit) {
		$filters = array(
			"priv_type" 		=> "default:table",
			"who" 					=> "default:0",
			"status" 				=> "default:0",
			"related_id" 		=> "default:0"
		);
		$permit['related_table'] = P($table);
		return $this->store("permits", $permit, $filters);
	}

	/**
	 * check that an action was called and no errors occurred
	 * @param string $model the model name
	 * @param string $action the function name
	 * @return bool true if the function was called without returning errors
	 */
	public function success($model, $action) { return (($_POST['action'][$model] == $action) && (empty($this->errors[$model]))); }
	
	/**
	 * mixin an object to import its functions into this object
	 * @param object $object the object to mixin
	 */
	protected function mixin($object) {
		$new_import = new $object();
		$import_name = get_class($new_import);
		$import_functions = get_class_methods($new_import);
		array_push($this->imported, array($import_name, $new_import));
		foreach($import_functions as $key => $function_name) $this->imported_functions[$function_name] = &$new_import;
	}

	/**
	 * Handler for calls to non-existant functions. Allows calling of mixed in functions.
	 */
	public function __call($method, $args) {
		if(array_key_exists($method, $this->imported_functions)) {
			$args[] = $this;  
			return call_user_func_array(array($this->imported_functions[$method], $method), $args);
		}
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}
}
?>
