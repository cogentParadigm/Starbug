<?php
	$record = (is_numeric($id)) ? query("uris", "select:uris.*  where:uris.id=?  limit:1", array($id)) : query("uris", "select:uris.*  limit:1  orderby:uris.modified DESC");
	$id = $record['id'];
	efault($_POST["uris"], array());
	if (empty($_POST["uris"]["categories"])) {
		$_POST["uris"]["categories"] = array();
		$cats = query("uris_categories", "where:uris_id=?", array($id));
		foreach ($cats as $c) $_POST["uris"]["categories"][] = $c['terms_id'];
	}
	foreach ($record as $k => $v) dfault($_POST["uris"][$k], $v);
	if (!empty($_POST["uris"]['id'])) $id = $_POST["uris"]['id'];
	assign("id", $id);
	assign("action", "update");
	assign("uri", "admin/uris");
	render_form("uris");
?>	
