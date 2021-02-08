<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;

class AdminOrdersController extends Controller {
  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models) {
    $this->db = $db;
    $this->models = $models;
  }
  public function details($id) {
    $this->assign("model", "orders");
    $this->assign("id", $id);
    $order = $this->models->get("orders")->load($id);
    $products = $this->models->get("product_lines")->query()->condition("orders_id", $order['id'])->select("SUM(price*qty) as total")->one();
    $shipping = $this->models->get("shipping_lines")->query()->condition("orders_id", $order['id'])->select("SUM(price*qty) as total")->one();
    $this->assign("order", $order);
    $this->assign("products", $products);
    $this->assign("shipping", $shipping);
    return $this->render("admin/orders/details.html");
  }
}
