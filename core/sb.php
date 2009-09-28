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
		//if (strpos($what, "core/") === 0) $what = "core/app/plugins".substr($what, 4);
		//else $what = "app/plugins/".$what;
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
			$this->objects[$name] = new $obj($this->db, $name);
		}
		return $this->objects[$name];
	}
	function has($name) {return (($this->objects[$name]) || (file_exists("app/models/".ucwords($obj).".php")));}
}
?>
