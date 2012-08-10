<?php
class AdminMenusController {
	function init() {
		assign("model", "menus");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("menus", "create")) redirect(uri("admin/menus/update", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		$this->render("admin/menus/update");
	}
}
?>
