<?php
$sb->provide("util/db");
function query($froms, $args, $mine=false) {
	global $sb;
	return $sb->query($froms, $args, $mine);
}
function raw_query($query) {
	global $sb;
	if (strtolower(substr($query, 0, 6)) == "select") return $sb->db->query($query);
	else return $sb->db->exec($query);
}
function store($name, $fields, $from="auto") {
	global $sb;
	return $sb->store($name, $fields, $from);
}
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
function remove($from, $where) {
	global $sb;
	return $sb->remove($from, $where);
}
?>
