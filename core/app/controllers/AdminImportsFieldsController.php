<?php
namespace Starbug\Core;
class AdminImportsFieldsController extends Controller {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "imports_fields");
		$this->assign("cancel_url", "admin/imports_fields");
	}
	function default_action() {
		$this->render("admin/list.html");
	}
	function create() {
		$this->render("admin/create.html");
	}
	function update($id) {
		$this->assign("id", $id);
		$this->render("admin/update.html");
	}
}
