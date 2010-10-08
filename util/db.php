<?php
/**
 * This file is part of StarbugPHP
 * @file util/db.php db function wrappers
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
$sb->provide("util/db");
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
		return $sb->store($name, $fields, $from);
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
?>
