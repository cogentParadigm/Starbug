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
 * utility import function
 * @ingroup core
 * @param string $util the utility
 * @param string $module the module
 */
function import($util, $module="util") {
	$sb->import($module."/".$util);
}
/**
 * @copydoc sb::query
 * @ingroup core
 */
function query($froms, $args="", $mine=true) {
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
 * @copydoc sb::queue
 * @ingroup core
 */
function queue($name, $fields, $from="auto") {
	global $sb;
	return $sb->queue($name, $fields, $from);
}
/**
 * @copydoc sb::store_queue
 * @ingroup core
 */
function store_queue() {
	global $sb;
	$sb->store_queue();
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
 * fetch any data validation errors
 * @return array errors indexed by model and field, empty if no errors
 * @ingroup core
 */
function errors() {
	global $sb;
	return $sb->errors;
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
/**
 * check if user is logged in
 * @ingroup core
 * @param string $group return true only if the user is in the specified group (optional)
 */
function logged_in($group="") {
	global $groups;
	return ((empty($group) && (!empty($_SESSION[P('id')]))) || ($_SESSION[P('memberships')] & $groups[$group]));
}
/**
 * get user info
 * @ingroup core
 * @param string $field the name of the field
 */
function userinfo($field="") {
	global $groups;
	if ("group" == $field) return array_search($_SESSION[P('user')]['collective'], $groups);
	else return $_SESSION[P("user")][$field];
}
/**
 * connect javascript events to css selectors
 * @ingroup core
 * @param star $ops the css selector, action: the js function, url: optional url for xhr submission, event: js event (default onclick)
 * @param star $params additional paramteres to be passed to your function
 */
function dojo_connect($ops, $params="") {
	$ops = starr::star($ops);
	$query = array_shift($ops);
	efault($ops['event'], 'onclick');
	global $sb;
	$sb->import("util/dojo");
	global $dojo;
	if (isset($ops['url'])) $dojo->xhr($query, $ops['action'], $ops['url'], $params, $ops['event']);
	$dojo->attach($query, $ops['action'], $params, $ops['event']);
}
/**
 * open a dojo dialog
 * @ingroup core
 * @param string $ops the css selector of the trigger, title: the js function, url: the url to load in the dialog
 */
function dojo_dialog($ops) {
	$ops = starr::star($ops);
	$query = array_shift($ops);
	efault($ops['event'], 'onclick');
	global $sb;
	$sb->import("util/dojo");
	global $dojo;
	$dojo->dialog($query, "title:$ops[title]  url:$args[url]", $args['event']);
}
/**
 * connect a link to trigger a form to be opened in a dojo dialog
 * @ingroup core
 * @param string $trigger a css selector, selecting the link(s)
 * @param star $args form: the url of the form, title: the title of the dialog, callback: the function to call after submitted
 */
function remote_form($trigger, $args) {
	global $sb;
	$sb->import("util/dojo");
	global $dojo;
	$args = starr::star($args);
	efault($args['form'], '');
	efault($args['title'], '');
	efault($args['callback'], 'null');
	$d = $dojo->dialog($trigger, "title:$args[title]  url:$args[form]");
	$dojo->xhr("#dijit_Dialog_$d form", "sb.post_form", $args['action'], "form:dojo.query('#dijit_Dialog_$d form')[0]  handleAs:'json'  callback:$args[callback]  close_dialog:$d", "onsubmit");
	$dojo->attach("#dijit_Dialog_$d form .cancel", "sb.close_dialog", "dialog:$d");
}
/**
 * check to see if this the front page
 * @ingroup core
 * @return bool true if it is, false if it isn't
 */
function is_home() {
	global $request;
	return ($request->path == Etc::DEFAULT_PATH);
}
?>