<?php
/**
* FILE: core/sb.php
* PURPOSE: The global object. provides data, errors, import/provide, load and pub/sub.
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
	var $recordCount;
	var $insert_id;
	var $provided = array();
	var $objects = array();
	var $errors = array();
	function sb() {
		$this->db = ADONewConnection('mysql');
		$this->db->Connect(Etc::DB_HOST, Etc::DB_USERNAME, Etc::DB_PASSWORD, Etc::DB_NAME);
		session_start();
		if (!isset($_SESSION[P('id')])) $_SESSION[P('id')] = $_SESSION[P('memberships')] = 0;
	}
	function load($what) {
		if (file_exists($what.".php")) include($what.".php");
		else {
			$token = split("/", $what); $token = $what."/".end($token).".php";
			if (file_exists($token)) include($token);
		}
	}
	function publish($topic, $args=null) {
		global $request;
		$args = func_get_args();
		foreach($request->tags as $tag) {
			$subscriptions = (file_exists("var/hooks/$tag[tag].$topic")) ? unserialize(file_get_contents("var/hooks/$tag[tag].$topic")) : array();
			foreach($subscriptions as $priority) foreach($priority as $handle) call_user_func(explode("::", $handle['handle']), $handle['args'], $args);
		}
	}
	function subscribe($topic, $tags, $priority, $handle, $args=null) {
		if (!is_array($tags)) $tags = array($tags);
		foreach ($tags as $tag) {
			$subscriptions = (file_exists("var/hooks/$tag.$topic")) ? unserialize(file_get_contents("var/hooks/$tag.$topic")) : array();
			$subscriptions[$priority][] = ($args == null) ? array("handle" => $handle, "args" => array()) : array("handle" => $handle, "args" => $args);
			$file = fopen("var/hooks/$tag.$topic", "wb");
			fwrite($file, serialize($subscriptions));
			fclose($file);
		}
	}
	function unsubscribe($topic, $tags, $priority, $handle) {
		if (!is_array($tags)) $tags = array($tags);
		foreach ($tags as $tag) {
			$subscriptions = (file_exists("var/hooks/$tag.$topic")) ? unserialize(file_get_contents("var/hooks/$tag.$topic")) : array();
			if (false !== ($index = array_search($handle, $subscriptions[$priority]))) unset($subscriptions[$priority][$index]);
			$file = fopen("var/hooks/$tag.$topic", "wb");
			fwrite($file, serialize($subscriptions));
			fclose($file);
		}
	}
	function import($loc) {global $sb; if (!$this->provided[$loc]) include($loc.".php");}
	function provide($loc) {$this->provided[$loc] = true;}
	function get($name) {
		$obj = ucwords($name);
		if (!$this->objects[$name]) {
			include("app/models/".$obj.".php");
			$this->objects[$name] = new $obj($name);
		}
		return $this->objects[$name];
	}
	function has($name) {return (($this->objects[$name]) || (file_exists("app/models/".ucwords($obj).".php")));}
	function query($froms, $args=array(), $mine=false) {
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
					if ($rel['type'] == "one") $from .= $namejoin."$rel[lookup].$rel[ref]=".(($rel['lookup'] == $first) ? $first : $f)."id";
					else if ($rel['type'] == "many") $from .= ($rel['lookup']) ? " INNER JOIN ".P($rel['lookup'])." AS $rel[lookup] ON ".$first.".id=$rel[lookup].$rel[hook]".$namejoin." $rel[lookup].$rel[ref]=$f.id" : $namejoin.$first.".id=$f.$rel[hook]";
				}
			}
		}
		$args = starr::star($args);
		efault($args['select'], "*");
		if ((!empty($args['action'])) && (($_SESSION[P("memberships")] & 1) != 1)) {
			if ($args['priv_type'] == "table") $from = P("permits")." AS permits";
			else $from .= " INNER JOIN ".P("permits")." AS permits";
			$args['where'] = "permits.related_table='".P($first)."'"
			." && permits.action='$args[action]'"
			." && ".(($args['priv_type'] == "table") ? "permits.priv_type='table'"
			:"(permits.priv_type='global' || (permits.priv_type='object' && permits.related_id=".$first.".id))"." && ((permits.status & ".$first.".status)=".$first.".status)")
			." && (permits.role='everyone' || (permits.role='user' && permits.who='".$_SESSION[P('id')]."') || (permits.role='group' && (('".$_SESSION[P('memberships')]."' & permits.who)=permits.who))"
			.(($args['priv_type'] == 'table') ? "" : " || (permits.role='owner' && ".$first.".owner='".$_SESSION[P('id')]."') || (permits.role='collective' && (('".$_SESSION[P('memberships')]."' & ".$first.".collective)=".$first.".collective))").")"
			.((empty($args['where'])) ? "" : " && ".$args['where']);
		}
		if (!empty($args['where'])) $args['where'] = " WHERE ".$args['where'];
		if (!(empty($args['limit']))) $limit = " LIMIT $args[limit]";
		//echo "SELECT $args[select] FROM $from$args[where]";
		$records = $this->db->Execute("SELECT $args[select] FROM $from$args[where]$limit");
		$this->recordCount = $records->RecordCount();
		return ($args['limit'] == 1) ? $records->fields() : $records->GetRows();
	}
	function store($name, $fields, $thefilters="mine") {
		if ($thefilters == "mine") $thefilters = ($this->has($name)) ? $this->get($name)->filters : array();
		$errors = array(); $byfilter = array();
		foreach ($fields as $col => $value) {
			$errors[$col] = array();
			$fields[$col] = trim($fields[$col]);
			$filters = starr::star($thefilters[$col]);
			foreach($filters as $filter => $args) $byfilter[$filter][$col] = $args;
			if ($value == "") $errors[$col]["required"] = "This field is required.";
		}
		foreach($byfilter as $filter => $args) {
			include("util/$filter.php");
		}
		foreach($errors as $col => $err) if (empty($err)) unset($errors[$col]);
		if(empty($errors)) { //no errors
			if(!empty($fields['id'])) { //updating existing record
				foreach($fields as $col => $value) {
					if ($col != 'id') {
						if(empty($setstr)) $setstr = $col."='".$value."'";
						else $setstr .= ", ".$col."='".$value."'";
					}
				}
				//echo "UPDATE $name SET ".$setstr." WHERE id='".$fields['id']."'";
				$this->db->Execute("UPDATE ".P($name)." SET ".$setstr." WHERE id='".$fields['id']."'");
			} else { //creating new record
				$keys = ""; $values = "";
				foreach($fields as $col => $value) {
					if(empty($keys)) $keys = $col;
					else $keys .= ", ".$col;
					$value = "'".$value."'";
					if(empty($values)) $values = $value;
					else $values .= ", ".$value;
				}
				//echo "INSERT INTO ".P($name)." (".$keys.") VALUES (".$values.")";
				$this->db->Execute("INSERT INTO ".P($name)." (".$keys.") VALUES (".$values.")");
				$this->insert_id = $this->db->Insert_ID();
			}
		}
		return $errors;
	}
	function remove($from, $where) {
		if (!empty($where)) {
			$records = $this->db->Execute("DELETE FROM ".P($from)." WHERE ".$where);
			$this->recordCount = $records->RecordCount();
		}
	}
	function post_act($key, $value) {
		if (($object = $this->get($key)) && method_exists($object, $value)) {
			$permits = isset($_POST[$key]['id']) ? $this->query($key, "action:$value	where:$key.id='".$_POST[$key]['id']."'") : $this->query($key, "action:$value	priv_type:table");
			if (($this->recordCount > 0) || ($_SESSION[P('memberships')] & 1)) $errors = $object->$value();
			else $errors = array("forbidden" => true);
			$this->errors = array_merge_recursive($this->errors, array($key => $errors));
			if (!empty($errors)) {
				global $request;
				$request->return_path();
			}
		}
	}
	function check_post() {if (!empty($_POST['action'])) foreach($_POST['action'] as $key => $val) $this->post_act($key, $val);
	}
	function grant($table, $permit) {
		$filters = array(
			"priv_type" 		=> "default:table",
			"who" 					=> "default:0",
			"status" 				=> "default:0",
			"related_id" 		=> "default:0"
		);
		$permit['related_table'] = P($table);
		$this->store("permits", $permit, $filters);
	}
}
?>
