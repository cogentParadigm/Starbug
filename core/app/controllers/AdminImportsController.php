<?php
class AdminImportsController {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->models = $models;
	}
	function init() {
		$this->assign("model", "imports");
		$this->assign("cancel_url", "admin/imports");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if ($this->db->success("imports", "create")) redirect(uri("admin/imports/update/".$this->models->get("imports")->insert_id, 'u'));
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("imports", "create")) redirect(uri("admin/imports", 'u'));
		else $this->render("admin/update");
	}
}
?>
