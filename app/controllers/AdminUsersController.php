<?php
class AdminUsersController {
	function init() {
		assign("model", "users");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		assign("form", "users");
		if (success("users", "create")) redirect(uri("admin/users/update", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		assign("form", "users");
		$this->render("admin/update");
	}
}
?>
