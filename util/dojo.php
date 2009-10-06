<?php
/**
* FILE: util/dojo.php
* PURPOSE: provides a tidy wrapper for dojo javascript toolkit
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
$sb->provide("util/dojo");
class dojo {
	var $behaviors = array();
	var $connects = array();
	function dojo() {
		global $request;
		$request->tags[] = array("tag" => "dojo", "raw_tag" => "dojo");
	}
	function behavior($query, $event, $action) {
		$merge = array($event => array($action));
		if (empty($this->behaviors[$query])) $this->behaviors[$query] = array();
		$this->behaviors[$query] = array_merge_recursive($this->behaviors[$query], $merge);
	}
	function xhr($query, $action, $url, $params="", $event="onclick") {
		$url = "'".uri("")."'+".$url;
		$this->attach($query, "sb.xhr", "action:$action	url:$url".(empty($params) ? "" : "\t".$params), $event);
	}
	function attach($query, $action, $params="", $event="onclick") {
		$params = starr::star($params);
		if (!empty($params['pre'])) {
			$object = $params['pre']."var args = {";
			unset($params['pre']);
		} else $object = "var args = {";
		if (!empty($params['form'])) efault($params['method'], "'post'");
		foreach ($params as $name => $value) $object .= $name." : ".$value.", ";
		$object .= "evt : evt};";
		//$object .= "console.log(args);";
		$action = $object.$action."({args : args});";
		$this->behavior($query, $event, $action);
	}
}
global $dojo;
$dojo = new dojo();
?>
