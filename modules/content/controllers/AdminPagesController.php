<?php
namespace Starbug\Content;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
class AdminPagesController extends Controller {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "pages");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if ($this->db->success("pages", "create")) $this->redirect("admin/pages");
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("pages", "create")) $this->redirect("admin/pages");
		else $this->render("admin/update");
	}
	function import() {
		$this->render("admin/import");
	}
}
