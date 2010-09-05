<?php
$sb->provide("util/db");
function query($froms, $args, $mine=false) {
	global $sb;
	return $sb->query($froms, $args, $mine);
}
function store($name, $fields, $thefilters="mine") {
	global $sb;
	return $sb->store($name, $fields, $thefilters);
}
function store_once($name, $fields, $thefilters="mine") {
	global $sb;
	if (!is_array($fields)) $fields = starr::star($fields);
	$where = "";
	foreach ($fields as $k => $v) {
		if (!empty($where)) $where .= " && ";
		$where .= "$k=".$sb->db->quote($v);
	}
	$records = $sb->query($name, "where:$where");
	if ($sb->record_count == 0) {
		return $sb->store($name, $fields, $thefilters);
	} else return false;
}
?>
