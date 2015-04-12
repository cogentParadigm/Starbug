<?php
class AdminPagesController {
	public $routes = array(
		'update' => '{id}'
	);
	function init() {
		$this->assign("model", "pages");
		$this->assign("cancel_url", "admin/pages");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("pages", "create")) redirect(uri("admin/pages", 'u'));
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if (success("pages", "create")) redirect(uri("admin/pages", 'u'));
		else $this->render("admin/update");
	}
}
?>
