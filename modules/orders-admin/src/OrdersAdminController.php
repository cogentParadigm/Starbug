<?php
namespace Starbug\Orders\Admin;

use Psr\Http\Message\ServerRequestInterface;
use Starbug\Db\DatabaseInterface;
use Starbug\Routing\Controller;

class OrdersAdminController extends Controller {
  public function __construct(
    protected DatabaseInterface $db
  ) {
  }
  public function __invoke($id, ServerRequestInterface $request) {
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
    $arguments = [
      "id" => $id,
      "order" => $order,
      "products" => $products,
      "shipping" => $shipping
    ];
    $arguments += $request->getAttribute("route")->getOptions();
    return $this->render("admin/orders/view.html", $arguments);
  }
}
