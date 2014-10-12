<?php
class AdminPagesController {
	function init() {
		assign("model", "pages");
		assign("cancel_url", "admin/pages");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("pages", "create")) redirect(uri("admin/pages", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		if (success("pages", "create")) redirect(uri("admin/pages", 'u'));
		else $this->render("admin/update");
	}
}
?>