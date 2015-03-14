<?php
class AdminUrisController {
	function init() {
		$this->assign("model", "uris");
		$this->assign("form", "uris");
		$this->assign("cancel_url", "admin/uris");
		if (success("uris", "create") || success("uris", "update")) {
			if ($_POST['operation'] == "save") redirect(uri("admin/uris", "u"));
			else if ($_POST['operation'] == "save_add_another") $_POST = array();
		}
	}
	function default_action() {
		$this->render("admin/list");
	}
	function add() {

	}
	function create() {
		$this->render("admin/create");
	}
	function update($id=null) {
		$this->assign("id", $id);
		$this->assign("action", "update");
		$this->render("admin/update");
	}
}
?>
