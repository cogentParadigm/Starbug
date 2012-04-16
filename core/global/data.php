<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/global/data.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup data
 */
/**
 * @defgroup data
 * global functions
 * @ingroup global
 */
/**
 * get single records or columns
 * @ingroup data
 * @param string $model the name of the model
 * @param string $id the id of the record
 * @param string $column optional column name
 */
function get() {
	$args = func_get_args();
	$count = count($args);
	if ($count == 1) return query($args[0]);
	else if ($count == 2) return query($args[0], "where:id='$args[1]'  limit:1");
	else if ($count == 3) return reset(query($args[0], "select:$args[2]  where:id='$args[1]'  limit:1"));
}
/**
	* getter/caller for model properties/functions
	* @ingroup data
	*/
function sb() {
	global $sb;
	$args = func_get_args();
	$count = count($args);
	if ($count == 0) return $sb;
	else if ($count == 1) {
		if ($sb->has($args[0])) return $sb->get($args[0]);
		else if (property_exists($sb, $args[0])) return $sb->$args[0];
	} else if ($count == 2) {
		return $sb->get($args[0])->$args[1];
	} else {
		$model = $sb->get(array_shift($args));
		$function = array_shift($args);
		return call_user_func_array(array($model, $function), $args);
	}
}
/**
 * @copydoc sb::query
 * @ingroup data
 */
function query($froms, $args="", $replacements=array()) {
	global $sb;
	return $sb->query($froms, $args, $replacements);
}
/**
 * perform a raw query
 * @ingroup data
 * @param string $query the sql query string
 */
function raw_query($query) {
	global $sb;
	$start = strtolower(substr(trim($query), 0, 6));
	if ($start == "select" || $start == "descri") return $sb->db->query($query);
	else return $sb->db->exec($query);
}
/**
 * @copydoc sb::store
 * @ingroup data
 */
function store($name, $fields, $from="auto") {
	global $sb;
	return $sb->store($name, $fields, $from);
}
/**
 * @copydoc sb::queue
 * @ingroup data
 */
function queue($name, $fields, $from="auto") {
	global $sb;
	return $sb->queue($name, $fields, $from);
}
/**
 * @copydoc sb::store_queue
 * @ingroup data
 */
function store_queue() {
	global $sb;
	$sb->store_queue();
}
/**
 * store only if a record with matching fields does not exist
 * @ingroup data
 * @copydoc sb::store
 */
function store_once($name, $fields, $from="auto") {
	global $sb;
	if (!is_array($fields)) $fields = star($fields);
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
 * @ingroup data
 */
function remove($from, $where) {
	global $sb;
	return $sb->remove($from, $where);
}
?>
