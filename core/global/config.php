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
/**
 * @copydoc sb::config
 * @ingroup config
 */
function &config($key, $value=null, $dir="etc/") {
	global $sb;
	return $sb->config($key, $value, $dir);
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
	efault($value['label'], ucwords($args[0]));
	efault($value['singular'], rtrim($args[0], 's'));
	efault($value['singular_label'], ucwords($value['singular']));
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
	efault($name, Etc::THEME);
	if (!empty($var)) $var = ".".$var;
	return config("info$var", null, "app/themes/$name/");
}
?>
