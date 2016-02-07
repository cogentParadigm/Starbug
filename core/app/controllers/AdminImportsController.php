<?php
namespace Starbug\Core;
class AdminImportsController extends Controller {
	public $routes = array(
		'update' => '{id}',
		'run' => '{id}'
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
		if ($this->db->success("imports", "create")) $this->redirect("admin/imports/update/".$this->models->get("imports")->insert_id);
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("imports", "create")) $this->redirect("admin/imports");
		else $this->render("admin/update");
	}
	function run($id) {
		$this->assign("id", $id);
		$this->render("admin/update", array("form_header" => "Run Import", "action" => "run"));
	}
}
?>
