<?php
class AdminSettingsController {
	function init() {
		assign("model", "settings");
	}
	function default_action() {
		assign("template", "settings");
		$this->render("admin/list");
	}
	function create() {
		if (success("settings", "create")) redirect(uri("admin/menus/update", 'u'));
		else $this->render("admin/create");
	}
	function update($id=null) {
		assign("id", $id);
		$this->render("admin/menus/update");
	}
}
?>
