<?php
	$id = end($request->uri);
	$record = (is_numeric($id)) ? query("uris", "select:uris.*  where:uris.id=?  limit:1", array($id)) : query("uris", "select:uris.*  limit:1  orderby:uris.modified DESC");
	efault($_POST["uris"], array());
	foreach ($record as $k => $v) dfault($_POST["uris"][$k], $v);
	if (!empty($_POST["uris"]['id'])) $id = $_POST["uris"]['id'];
	assign("id", $id);
	assign("action", "update");
	assign("uri", "admin/uris");
	render_form("uris");
?>	
