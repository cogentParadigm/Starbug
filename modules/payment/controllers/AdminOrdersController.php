<?php
namespace Starbug\Payment;
use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
class AdminOrdersController extends Controller {
	public $routes = array(
		"update" => "{id}",
		"details" => "{id}"
	);
	function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
		$this->db = $db;
		$this->models = $models;
	}
	function init() {
		$this->assign("model", "orders");
		$this->assign("cancel_url", "admin/orders");
	}
	function default_action() {
		$this->render("admin/list");
	}
	function details($id) {
		$this->assign("id", $id);
		$order = $this->models->get("orders")->load($id);
		$products = $this->models->get("product_lines")->query()->condition("orders_id", $order['id'])->select("SUM(price*qty) as total")->one();
		$shipping = $this->models->get("shipping_lines")->query()->condition("orders_id", $order['id'])->select("SUM(price*qty) as total")->one();
		$this->assign("order", $order);
		$this->assign("products", $products);
		$this->assign("shipping", $shipping);
		$this->render("admin/orders/details");
	}
	function update($id) {
		$this->assign("id", $id);
		if ($this->db->success("orders", "create")) $this->redirect("admin/orders");
		else $this->render("admin/update");
	}
}
