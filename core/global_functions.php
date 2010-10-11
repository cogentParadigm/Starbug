<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global_functions.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
/**
 * set a variable only if it is empty or not numeric
 * @ingroup core
 * @param mixed $val the variable to set, passed by reference
 * @param mixed $default the value to set the variable to if it turns out to be empty or not numeric
 */
function empty_nan(&$val, $default="") {if(!isset($val) || !is_numeric($val)) $val = $default;}
/**
 * set a variable only if it is not set
 * @ingroup core
 * @param mixed $val the variable to set, passed by reference
 * @param mixed $default the value to set the variable to if it turns out to not unset
 */
function dfault(&$val, $default="") {if(!isset($val)) $val = $default;return $val;}
/**
 * set a variable only if it is empty
 * @ingroup core
 * @param mixed $val the variable to set, passed by reference
 * @param mixed $default the value to set the variable to if it turns out to be empty
 */
function efault(&$val, $default="") {if(empty($val)) $val = $default;return $val;}
/**
 * just returns back a variable
 * @ingroup core
 * @param mixed $val the value to return
 * @return mixed $val
 */
function return_it($val) {return $val;}
/**
 * prefix a variable with the site prefix
 * @ingroup core
 * @param string $var the value to prefix
 * @return string the prefixed value
 */
function P($var) {return Etc::PREFIX.$var;}
/**
 * get an absolute URI from a relative path
 * @ingroup core
 * @param string $path the relative path
 * @param string $flags modification flag such as 's' for secure or 'f' for friendly
 * @return string the absolute path
 */
function uri($path="", $flags="") {
	if ($flags == "s") $prefix = "https://";
	else if ($flags == "f") $prefix = "";
	else $prefix = "http://";
	return $prefix.Etc::WEBSITE_URL.$path;
}
/**
 * @copydoc sb::query
 * @ingroup core
 */
function query($froms, $args="", $mine=false) {
	global $sb;
	return $sb->query($froms, $args, $mine);
}
/**
 * perform a raw query
 * @param string $query the sql query string
 * @ingroup core
 */
function raw_query($query) {
	global $sb;
	if (strtolower(substr($query, 0, 6)) == "select") return $sb->db->query($query);
	else return $sb->db->exec($query);
}
/**
 * @copydoc sb::store
 * @ingroup core
 */
function store($name, $fields, $from="auto") {
	global $sb;
	return $sb->store($name, $fields, $from);
}
/**
 * store only if a record with matching fields does not exist
 * @copydoc sb::store
 * @ingroup core
 */
function store_once($name, $fields, $from="auto") {
	global $sb;
	if (!is_array($fields)) $fields = starr::star($fields);
	$where = "";
	foreach ($fields as $k => $v) {
		if (!empty($where)) $where .= " && ";
		$where .= "$k=".$sb->db->quote($v);
	}
	$records = $sb->query($name, "where:$where");
	if ($sb->record_count == 0) {
		$err = $sb->store($name, $fields, $from);
		return $err;
	} else return false;
}
/**
 * @copydoc sb::remove
 * @ingroup core
 */
function remove($from, $where) {
	global $sb;
	return $sb->remove($from, $where);
}
/**
 * shortcut function for outputing small forms
 * @params string $arg the first parameter is the form options, the rest are form controls
 * @return string the form
 * @ingroup form
 */
function form($arg) {
	global $sb;
	$sb->import("util/form");
	$args = func_get_args();
	$init = array_shift($args);
	$form = new form($init);
	$data = $form->open();
	foreach($args as $field) {
		$parts = explode("  ", $field, 2);
		$name = $parts[0];
		$ops = starr::star($parts[1]);
        $before = $after = "";
        if (isset($ops['before'])) { $before = $ops['before']; unset($ops['before']); }
        if (isset($ops['after'])) { $after = $ops['after']; unset($ops['after']); }
        $str = "";
        foreach($ops as $k => $v) $str .= "$k:$v  ";
        $data .= $before.$form->$name(trim($str)).$after;
	}
	$data .= "</form>";
	return $data;
}
/**
 * creates a new form and outputs the opening form tag and some hidden inputs
 * @param string $options the options for the form
 * @param string $atts attributes for the form tag
 * @ingroup form
 */
function open_form($options, $atts="") {
	global $sb;
	$sb->import("util/form");
	global $global_form;
	$global_form = new form($options);
	$open = "";
	$atts = starr::star($atts);
	foreach($atts as $k => $v) $open .= $k.'="'.$v.'" ';
	echo $global_form->open(rtrim($open, " "));
}
/**
 * creates an HTML tag from a star (see star function)
 * @param string $tag the tag string eg. 'a href:/shop  style:font-weight:bold  content:Shop'
 * @param bool $self if true, will be treated as self closing tag '/>'
 */
function tag($tag, $self=false) {
	if (is_array($tag)) $name = array_shift($tag);
	else {
		$parts = explode("  ", $tag, 2);
		$name = $parts[0];
		if (count($parts) > 1) $tag = starr::star($parts[1]);
	}
	$echo = $tag['echo']; unset($tag['echo']);
	$content = $tag['content']; unset($tag['content']); $str = "";
	foreach($tag as $key => $value) $str .= " $key=\"$value\"";
	$return = ($self) ? "<$name$str />" : "<$name$str>$content</$name>";
	if ('false' !== $echo) echo $return;
	return $return;
}
/**
 * @copydoc starr::star
 * @ingroup core
 */
function star($str) {
	return starr::star($str);
}
?>