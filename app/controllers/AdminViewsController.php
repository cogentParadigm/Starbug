<?php
class AdminViewsController {
	public $routes = array(
		'update' => '{id}'
	);
	function init() {
		$this->assign("model", "views");
		$this->assign("cancel_url", "admin/views");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if (success("views", "create")) redirect(uri("admin/views", 'u'));
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if (success("views", "create")) redirect(uri("admin/views", 'u'));
		else $this->render("admin/update");
	}
}
?>
