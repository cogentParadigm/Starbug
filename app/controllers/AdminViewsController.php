<?php
namespace Starbug\App;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
class AdminViewsController extends Controller {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "views");
		$this->assign("cancel_url", "admin/views");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if ($this->db->success("views", "create")) $this->redirect("admin/views");
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("views", "create")) $this->redirect("admin/views");
		else $this->render("admin/update");
	}
	function import() {
		$this->render("admin/import");
	}
}
?>
