<?php
/**
* FILE: core/sb.php
* PURPOSE: The global $sb object. provides data, errors, import/provide, load and pub/sub. The backbone of Starbug.
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
class sb {
	var $db;
	var $record_count;
	var $insert_id;
	var $provided = array();
	var $objects = array();
	var $errors = array();
	var $alerts = array();
	var $imported = array();
	var $imported_functions = array();

	# constructor. connects to database and initializes the session.
	function sb() {
		$this->db = new db('mysql:host='.Etc::DB_HOST.';dbname='.Etc::DB_NAME, Etc::DB_USERNAME, Etc::DB_PASSWORD);
		$this->db->set_debug(true);
		if (!isset($_SESSION[P('id')])) $_SESSION[P('id')] = $_SESSION[P('memberships')] = 0;
	}

	# since you can't subscribe the handle 'include', use sb::load
	# @param what - 'jsforms' works for either 'jsforms.php' or 'jsforms/jsforms.php'
	function load($what) {
		if (file_exists($what.".php")) include($what.".php");
		else {
			$token = split("/", $what); $token = $what."/".end($token).".php";
			if (file_exists($token)) include($token);
		}
	}

	# publish a topic to any subscribers
	# @param topic - a string value for subscribers to subscribe to
	# @params args - any additional parameters will be passed in an array to the subscriber
	function publish($topic, $args=null) {
		global $request;
		$return = array();
		$args = func_get_args();
		$tags = (isset($request->tags)) ? $request->tags : array(array("tag" => "global"));
		foreach($tags as $tag) {
			$subscriptions = (file_exists("var/hooks/$tag[tag].$topic")) ? unserialize(file_get_contents("var/hooks/$tag[tag].$topic")) : array();
			foreach($subscriptions as $priority) foreach($priority as $handle) $return[] = call_user_func(explode("::", $handle['handle']), $handle['args'], $args);
		}
		return $return;
	}

	# import function. only imports once when used with provide
	# @param loc - path of file to import without '.php' at the end
	function import($loc) {global $sb; $args = func_get_args(); foreach($args as $l) if (!$this->provided[$l]) include(BASE_DIR."/".$l.".php");}

	# when imported use provide to prevent further imports from attempting to include it again
	# @param loc - the imported location. if i were to use $sb->import("util/form"), util/form.php would have $sb->provide("util/form") at the top
	function provide($loc) {$this->provided[$loc] = true;}

	# get a model by name
	# @param name - the name of the model, such as 'users'
	function get($name) {
		$obj = ucwords($name);
		if (!isset($this->objects[$name])) {
			include(BASE_DIR."/app/models/".$obj.".php");
			$this->objects[$name] = new $obj($name);
		}
		return $this->objects[$name];
	}

	# check if a model exists
	# @param name (the name of the model), @return bool (true if model exists, false otherwise)
	function has($name) {return (($this->objects[$name]) || (file_exists(BASE_DIR."/app/models/".ucwords($name).".php")));}

	# query the database
	# @param froms - comma delimeted list of tables to join. 'users' or 'uris,system_tags'
	# @param args - tab-colon query string for params: select, where, limit, and action/priv_type
	# @param mine - optional. if true, joining models will be checked for relationships and ON statements will be added
	function query($froms, $args="", $mine=false) {
		$froms = explode(",", $froms);
		$first = array_shift($froms);
		$from = "`".P($first)."` AS `".$first."`";
		if (!$mine) foreach ($froms as $f) $from .= " INNER JOIN `".P($f)."` AS `".$f."`";
		else {
			$relations = $this->get($first)->relations;
			foreach ($froms as $f) {
				$rel = $relations[$f];
				$namejoin = " INNER JOIN `".P($f)."` AS `$f`";
				if (empty($rel)) $from .= $namejoin;
				else {
					$namejoin .= " ON ";
					if ($rel['type'] == "one") $from .= $namejoin."$rel[lookup].$rel[ref]=".(($rel['lookup'] == $first) ? $f : $first).".id";
					else if ($rel['type'] == "many") $from .= ($rel['lookup']) ? " INNER JOIN ".P($rel['lookup'])." AS $rel[lookup] ON ".$first.".id=$rel[lookup].$rel[hook]".$namejoin." $rel[lookup].$rel[ref]=$f.id" : $namejoin.$first.".id=$f.$rel[hook]";
				}
			}
		}
		$args = starr::star($args);
		efault($args['select'], "*");
		if ((!empty($args['action'])) && (($_SESSION[P("memberships")] & 1) != 1)) {
			$roles = "(permits.role='everyone' || (permits.role='user' && permits.who='".$_SESSION[P('id')]."') || (permits.role='group' && (('".$_SESSION[P('memberships')]."' & permits.who)=permits.who))";
			if ((!empty($args['priv_type'])) && ($args['priv_type'] == "table")) {
				$from = P("permits")." AS permits";
				$permit_type = "permits.priv_type='table'";
			} else {
				$from .= " INNER JOIN ".P("permits")." AS permits";
				$permit_type = "(permits.priv_type='global' || (permits.priv_type='object' && permits.related_id=".$first.".id))"." && ((permits.status & ".$first.".status)=".$first.".status)";
				$roles .= " || (permits.role='owner' && ".$first.".owner='".$_SESSION[P('id')]."') || (permits.role='collective' && (('".$_SESSION[P('memberships')]."' & ".$first.".collective)=".$first.".collective))";
			}
			$args['where'] = "permits.related_table='".P($first)."'"
			." && permits.action='$args[action]'"
			." && ".$permit_type
			." && ".$roles.")"
			.((empty($args['where'])) ? "" : " && ".$args['where']);
		}
		if (!empty($args['keywords'])) {
			$this->import("util/search");
			$args['where'] = ((empty($args['where'])) ? "" : $args['where']." && ").keywordClause($args['keywords'], split(",", $args['search']));
		}
		if (!empty($args['where'])) $args['where'] = " WHERE ".$args['where'];
		$limit = (!(empty($args['limit']))) ? " LIMIT $args[limit]" : "";
		$order = (!(empty($args['orderby']))) ? " ORDER BY $args[orderby]" : "";
		$sql = " FROM $from$args[where]$order$limit";
		try {
			$res = $this->db->query("SELECT COUNT(*)".$sql);
			$this->record_count = $res->fetchColumn();
			$records = $this->db->prepare("SELECT $args[select] FROM $from$args[where]$order$limit");
			$records->execute();
		} catch(PDOException $e) {
			die("DB Exception: ".$e->getMessage());
		}
		return ((!empty($args['limit'])) && ($args['limit'] == 1)) ? $records->fetch() : $records->fetchAll(PDO::FETCH_ASSOC);
	}

	# store data in the database
	# @param name - the name of the table
	# @param fields - an associative array representation of the record or update
	# @param thefilters - optional. filters to apply on the data. if not passed and a model exists, it will be checked for filters
	function store($name, $fields, $thefilters="mine") {
		if ($thefilters == "mine") $thefilters = ($this->has($name)) ? $this->get($name)->filters : array();
		$errors = array(); $byfilter = array();
		if (!is_array($fields)) $fields = starr::star($fields);
		foreach ($fields as $col => $value) {
			$errors[$col] = array();
			$fields[$col] = trim($fields[$col]);
			$filters = starr::star($thefilters[$col]);
			foreach($filters as $filter => $args) $byfilter[$filter][$col] = $args;
			if ($value === "") $errors[$col]["required"] = "This field is required.";
		}
		foreach($byfilter as $filter => $args) {
			include(BASE_DIR."/util/filters/$filter.php");
		}
		foreach($errors as $col => $err) if (empty($err)) unset($errors[$col]);
		if((empty($errors)) && (empty($this->errors[$name]))) { //no errors
			$prize = array();
			$fields['modified'] = date("Y-m-d H:i:s");
			if(!empty($fields['id'])) { //updating existing record
				foreach($fields as $col => $value) {
					if ($col != 'id') {
						$prize[] = $value;
						if(empty($setstr)) $setstr = $col."= ?";
						else $setstr .= ", ".$col."= ?";
					}
				}
				$stmt = $this->db->prepare("UPDATE ".P($name)." SET ".$setstr." WHERE id='".$fields['id']."'");
				$this->record_count = $stmt->execute($prize);
			} else { //creating new record
				$fields['created'] = date("Y-m-d H:i:s");
				if (!isset($fields['owner'])) $fields['owner'] = $_SESSION[P('id')];
				$keys = ""; $values = "";
				foreach($fields as $col => $value) {
					$prize[] = $value;
					if(empty($keys)) $keys = $col;
					else $keys .= ", ".$col;
					if(empty($values)) $values = "?";
					else $values .= ", ?";
				}
				$stmt = $this->db->prepare("INSERT INTO ".P($name)." (".$keys.") VALUES (".$values.")");
				$this->record_count = $stmt->execute($prize);
				$this->insert_id = $this->db->lastInsertId();
			}
		}
		return $errors;
	}

	# remove from the database
	# @param from (the name of the table), @param where (the WHERE conditions on the DELETE)
	function remove($from, $where) {
		if (!empty($where)) {
			try {
				$del = $this->db->prepare("DELETE FROM ".P($from)." WHERE ".$where);
				$del->execute();
				$this->record_count = $del->rowCount();
			} catch(PDOException $e) {
				die("DB Exception: ".$e->getMessage());
			}
		}
	}

	# run a model action if permitted
	# @param key (the model name), @param value (the function name)
	function post_act($key, $value) {
		if (($object = $this->get($key)) && method_exists($object, $value)) {
			$permits = isset($_POST[$key]['id']) ? $this->query($key, "action:$value  where:$key.id='".$_POST[$key]['id']."'") : $this->query($key, "action:$value  priv_type:table");
			if (($this->record_count > 0) || ($_SESSION[P('memberships')] & 1)) $errors = $object->$value();
			else $errors = array("forbidden" => "You do not have sufficient permission to complete your request.");
			$this->errors = array_merge_recursive($this->errors, array($key => $errors));
			if (!empty($this->errors[$key])) {
				global $request;
				$request->return_path();
			}
		}
	}

	# check $_POST['action'] for posted actions and run them through post_act
	function check_post() {if (!empty($_POST['action'])) foreach($_POST['action'] as $key => $val) $this->post_act($key, $val);}

	# grant permissions
	# @param table (the table on which to apply the permit), @param permit (an associate array of the permit record)
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

	# check that an action was called and no errors occurred
	public function success($model, $action) { return (($_POST['action'][$model] == $action) && (empty($this->errors[$model]))); }
	
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
