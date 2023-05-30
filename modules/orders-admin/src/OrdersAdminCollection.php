<?php
namespace Starbug\Orders\Admin;

use Starbug\Db\DatabaseInterface;
use Starbug\Core\AdminCollection;
use Starbug\Price\FormatterInterface;

class OrdersAdminCollection extends AdminCollection {
  public function __construct(DatabaseInterface $db, FormatterInterface $formatter) {
    $this->db = $db;
    $this->formatter = $formatter;
  }
  public function build($query, $ops) {
    $query->select(["id", "order_status", "created"], "orders");
    $query->leftJoin("payments")->on("payments.orders_id=orders.id");
    $query->leftJoin("lines")->on("lines.orders_id=orders.id");
    $query->group("orders.id");
    // selections
    $query->select("SUM(CASE WHEN lines.type='coupon_lines' THEN -1 * lines.price ELSE lines.price END * lines.qty) as total");
    $query->select("COALESCE(MAX(payments.created), '0000-00-00 00:00:00') as purchased");
    $query->select("CASE WHEN orders.billing_address is null THEN orders.shipping_address.recipient ELSE orders.billing_address.recipient END as customer");
    // filters
    if (!empty($ops["order_status"])) {
      $query->condition("orders.order_status", explode(",", $ops['order_status']));
    }
    // sorting
    if (empty($ops["sort"])) {
      $ops["sort"] = "purchased DESC, orders.created DESC, orders.id DESC";
    }
    $query->sort($ops["sort"]);
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $row) {
      $rows[$idx]["total_formatted"] = $this->formatter->format($row['total']);
    }
    return $rows;
  }
}
