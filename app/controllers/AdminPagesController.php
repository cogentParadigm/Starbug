<?php
class AdminPagesController {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "pages");
		$this->assign("cancel_url", "admin/pages");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if ($this->db->success("pages", "create")) redirect(uri("admin/pages", 'u'));
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("pages", "create")) redirect(uri("admin/pages", 'u'));
		else $this->render("admin/update");
	}
}
?>
