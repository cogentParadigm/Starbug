<?php
namespace Starbug\Payment;

use Starbug\Core\Controller;
use Starbug\Core\DatabaseInterface;

class AdminOrdersController extends Controller {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function details($id) {
    $this->assign("model", "orders");
    $this->assign("id", $id);
    $order = $this->db->get("orders", $id);
    $products = $this->db->query("product_lines")
      ->innerJoin("lines")->on("lines.id=product_lines.lines_id")
      ->condition("orders_id", $order['id'])
      ->select("lines.*,product_lines.product")
      ->select("SUM(price*qty) as total")->one();
    $shipping = $this->db->query("shipping_lines")
      ->innerJoin("lines")->on("lines.id=shipping_lines.lines_id")
      ->condition("orders_id", $order['id'])
      ->select("lines.*,shipping_lines.method")
      ->select("SUM(price*qty) as total")->one();
    $this->assign("order", $order);
    $this->assign("products", $products);
    $this->assign("shipping", $shipping);
    return $this->render("admin/orders/details.html");
  }
}
