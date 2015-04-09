<?php
class AdminUsersController {
	public $routes = array(
		'update' => '{id}'
	);
	function init() {
		$this->assign("model", "users");
		$this->assign("cancel_url", "admin/users");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		$this->assign("form", "users");
		if (success("users", "create")) redirect(uri("admin/users", 'u'));
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		$this->assign("form", "users");
		$this->render("admin/update");
	}
}
?>
