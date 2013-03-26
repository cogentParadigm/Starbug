<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/js.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup js
 */
/**
 * @defgroup js
 * js utility
 * @ingroup lib
 */
$sb->provide("core/lib/js");
/**
 * The js class. A javascript helper class
 * @ingroup js
 */
class js {
	var $behaviors = array();
	var $requires = array();
	function __construct() {
		global $request;
		$request->tags[] = array("term" => "js", "slug" => "js");
	}
	function require_js($mid) {
		$this->requires[] = $mid;
	}
	function behavior($query, $event, $action) {
		$merge = array($event => array($action));
		if (empty($this->behaviors[$query])) $this->behaviors[$query] = array();
		$this->behaviors[$query] = array_merge_recursive($this->behaviors[$query], $merge);
	}
	function xhr($query, $action, $url, $params="", $event="onclick") {
		if (false === strpos($url, $base)) $url = "'$base'+$url";
		$this->attach($query, "sb.xhr", "action:$action  url:$url".(empty($params) ? "" : "  ".$params), $event);
	}
	function attach($query, $action, $params="", $event="onclick") {
		$params = star($params);
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
	function column($field, $ops=array()) {
		$ops = star($ops);

		//set editor
		if (!empty($ops['editor'])) {
			if (!empty($ops['editorArgs'])) $ops['editorArgs'] = json_decode($ops['editorArgs'], true);
			if ($ops['editor'] == "text") $ops['editor'] = "'text'";
			else if ($ops['editor'] == "select") {
				$this->require_js("dijit/form/Select");
				$ops['editor'] = "dijit.form.Select";
				if (!isset($ops['editorArgs'])) $ops['editorArgs'] = array();
				$ops['editorArgs']['style'] = "'width:100%;'";
			} else if ($ops['editor'] == "bool") {
				$this->require_js("dijit/form/Select");
				$ops['editor'] = "dijit.form.Select";
				$ops['editorArgs'] = array("options" => "[{value:1, label:'Yes'},{value:0, label:'No'}]", "style" => "'width:50px'");
			}
		}
		
		//format editorArgs
		if (is_array($ops['editorArgs'])) {
			$args = array();
			foreach ($ops['editorArgs'] as $k => $v) $args[] = $k.":".$v;
			$ops['editorArgs'] = "{".implode(", ", $args)."}";
		}
		
		//generate column string and return
		$column = "field:'$field'";
		foreach ($ops as $k => $v) $column .= "  $k:$v";
		return $column;
	}
}
/**
 * A global instance of the dojo class
 * @ingroup dojo
 */
global $js;
$js = new js();
?>
