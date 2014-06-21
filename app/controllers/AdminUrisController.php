<?php
class AdminUrisController {
	function init() {
		assign("model", "uris");
		assign("form", "uris");
		assign("cancel_url", "admin/uris");
		if (success("uris", "create")) redirect(uri("admin/uris", "u"));
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		$this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		$this->render("admin/update");
	}
}
?>
