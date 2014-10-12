<?php
class AdminViewsController {
	function init() {
		assign("model", "views");
		assign("cancel_url", "admin/views");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("views", "create")) redirect(uri("admin/views", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		if (success("views", "create")) redirect(uri("admin/views", 'u'));
		else $this->render("admin/update");
	}
}
?>
