<?php
	$record = (is_numeric($id)) ? get("uris", $id) : get("uris", array(), array("sort" => array("modified" => -1), "limit" => 1));
	$id = $record['id'];
	efault($_POST["uris"], array());
	if (empty($_POST["uris"]["categories"])) {
		$_POST["uris"]["categories"] = array();
		$cats = get("uris_categories", array("uris_id" => $id));
		foreach ($cats as $c) $_POST["uris"]["categories"][] = $c['terms_id'];
	}
	foreach ($record as $k => $v) dfault($_POST["uris"][$k], $v);
	if (!empty($_POST["uris"]['id'])) $id = $_POST["uris"]['id'];
	assign("id", $id);
	assign("action", "update");
	assign("uri", "admin/uris");
	render_form("uris");
?>	
