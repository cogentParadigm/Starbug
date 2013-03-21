<?php
class AdminUsersController {
	function init() {
		assign("model", "users");
	}
	function default_action() {
		assign("columns", array(
			"Memberships" => "field:'memberships'  plugin:starbug.grid.columns.groups"
		));
		$this->render("admin/list");
	}
	function create() {
		if (success("users", "create")) redirect(uri("admin/users/update", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		$this->render("admin/update");
	}
}
?>