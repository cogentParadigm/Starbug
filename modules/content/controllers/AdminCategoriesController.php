<?php
namespace Starbug\Content;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
class AdminCategoriesController extends Controller {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "categories");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if ($this->db->success("categories", "create")) {
			$this->redirect("admin/categories");
		} else {
			$this->render("admin/create");
		}
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("categories", "create")) {
			$this->redirect("admin/categories");
		} else {
			$this->render("admin/update");
		}
	}
	function import() {
		$this->render("admin/import");
	}
}
?>
