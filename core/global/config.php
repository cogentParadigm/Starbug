<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/config.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup config
 */
/**
 * @defgroup config
 * global functions
 * @ingroup global
 */
global $config;
$config = array();
/**
 * get or set config variables from json files
 * @TODO clean this mess up.. perhaps splitting get/set operations
 * @param string $key the file name and optional sub indices. for example, 'themes' or 'fixtures.base'
 * providing first.second.third will open up the file first.json and look for the key "second" and within that, a key "third"
 */
function &config($key, $value=null, $dir="etc/") {
	global $config;
	
	//keys needed to walk the array
	$parts = explode(".", $key);
	
	//file name
	$key = array_shift($parts);
	if (!file_exists(BASE_DIR."/$dir$key.json")) return array();
	
	//get text
	$text = file_get_contents(BASE_DIR."/$dir$key.json");
	
	//strip comments and decode json
	if (empty($config[$key])) {
		$raw = explode("\n", $text);
		foreach ($raw as $idx => $item) {
			$first = substr(trim($item), 0, 1);
			if (!(in_array($first, array('"', '{', '}', '[', ']')) || is_numeric($first))) unset($raw[$idx]);
		}
		$config[$dir.$key] = json_decode(join("\n", $raw), true);
	}
	
	//walk to the value, tracking our position as we go
	//we'll end up with the position of the key to change or add to
	$conf = &$config[$dir.$key];
	$position = 0;
	$action = "change";
	while (!empty($parts)) {
		$action = "change";
		$next = array_shift($parts);
		preg_match('/"'.$next.'"\s*:/', $text, $matches, PREG_OFFSET_CAPTURE, $position);
		if (!empty($matches)) {
			$position = $matches[0][1];
			$k = $next;
		} else $action = "add";
		$conf = &$conf[$next];
	}

	//set the value
	if ($value != null) {
		$matches = array();
		preg_match('/["\[{]/', $text, $matches, PREG_OFFSET_CAPTURE, $position+strlen($k)+3);
		if ($action == "change") {
			switch ($matches[0][0]) {
				case '"':
					$text = substr($text, 0, $matches[0][1]+1).$value.substr($text, $matches[0][1]+strlen($conf)+1);
					break;
				case '[':
					print_r($conf);
					$closer = ']';
				case '{':
					$closer = '}';
				default:
					
			}
		} else if ($action == "add") {
			switch ($matches[0][0]) {
				case '"':
					break;
				case '[':
					preg_match_all('/\[/', $text, $opens, PREG_OFFSET_CAPTURE, $matches[0][1]+1);
					preg_match_all('/["\[{]\s*\]/', $text, $closes, PREG_OFFSET_CAPTURE, $matches[0][1]+1);
					$end = count($opens) - 1;
					$space = substr($closes[0][$end][0], 1, strlen($closes[0][$end][0])-2);
					if (!empty($space)) $space .= "\t";
					$text = substr($text, 0, $closes[0][$end][1]+1).",$space".json_encode($value).substr($text, $closes[0][$end][1]+1);
					break;
				case '{':
					preg_match_all('/{/', $text, $opens, PREG_OFFSET_CAPTURE, $matches[0][1]+1);
					preg_match_all('/["\[{]\s*}/', $text, $closes, PREG_OFFSET_CAPTURE, $matches[0][1]+1);
					$end = count($opens) - 1;
					$space = substr($closes[0][$end][0], 1, strlen($closes[0][$end][0])-2);
					if (!empty($space)) $space .= "\t";
					$text = substr($text, 0, $closes[0][$end][1]+1).",$space".'"'.$next.'":'.json_encode($value).substr($text, $closes[0][$end][1]+1);
					break;
			}
		}
		$conf = $value;
		file_put_contents(BASE_DIR."/$dir$key.json", $text);
	}
	return $conf;
}
/**
 * get schema data for a model
 * @ingroup config
 * @param $model the model/table
 * @return array schematic data for the model
 */
function schema($model) {
	$args = func_get_args();
	$count = count($args);
	$value = config($args[0], null, "var/json/");
	efault($value['label'], ucwords(str_replace(array("-", "_"), array(" ", " "), $args[0])));
	efault($value['singular'], rtrim($args[0], 's'));
	efault($value['singular_label'], ucwords(str_replace(array("-", "_"), array(" ", " "), $value['singular'])));
	efault($value['list'], "only");
	if ($count == 1) return $value;
	else if ($count == 2) return $value[$args[1]];
	else return false;
}
/**
	* get theme variables
	* @ingroup config
	* @param string $var the theme variable to get, if empty return the whole theme info object
	*/
function theme($var="", $name="") {
	efault($name, request("theme"));
	efault($name, settings("theme"));
	if (!empty($var)) $var = ".".$var;
	return config("info$var", null, "app/themes/$name/");
}
/**
	* get module variables
	* @ingroup config
	* @param string $var the module variable to get, if empty return the whole module info object
	*/
function module($key) {
	$key = explode(".", $key, 2);
	$module = $key[0];
	$var = (isset($key[1])) ? ".".$key[1] : "";
	return config("info$var", null, "modules/$module/");
}
/**
	* get/set site options
	* @ingroup config
	* @param string $name the option name
	* @param mixed $value (optional) value to set
	* @param bool $update pass true to force update the value
	*/
function settings($name, $value=null, $update=false) {
	if ($value == null) {
		if (!$update && is_cached("settings-".$name)) return cache($name);
		else {
			$value = query("settings", "where:name='$name'  limit:1");
			cache("settings-".$name, $value['value']);
			return $value['value'];
		}
	} else {
		store("settings", "value:$value", "name:$name");
		cache("settings-".$name, $value);
		return $value;
	}
}
?>
