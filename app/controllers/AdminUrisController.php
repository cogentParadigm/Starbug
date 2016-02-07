<?php
namespace Starbug\App;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
class AdminUrisController extends Controller {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "uris");
		$this->assign("form", "uris");
		$this->assign("cancel_url", "admin/uris");
		if ($this->db->success("uris", "create") || $this->db->success("uris", "update")) {
			if ($this->request->getPost('operation') == "save") $this->redirect("admin/uris");
			else if ($this->request->getPost('operation') == "save_add_another") $this->request->setPost(array());
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
	function update($id) {
		$this->assign("id", $id);
		$this->assign("action", "update");
		$this->render("admin/update");
	}
}
?>
