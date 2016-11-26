<?php
namespace Starbug\Payment;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
class AdminProductsController extends Controller {
	public $routes = array(
		'update' => '{id}'
	);
	function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	function init() {
		$this->assign("model", "products");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function create() {
		if ($this->db->success("products", "create")) $this->redirect("admin/products");
		else $this->render("admin/create");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("products", "create")) $this->redirect("admin/products");
		else $this->render("admin/update");
	}
}
