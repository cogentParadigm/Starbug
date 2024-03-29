<?php
namespace Starbug\Payment;

use Starbug\Db\Collection;
use Starbug\Db\DatabaseInterface;

class ShippingLinesCollection extends Collection {
  protected $model = "shipping_lines";
  public function __construct(DatabaseInterface $db, PriceFormatterInterface $formatter) {
    $this->db = $db;
    $this->formatter = $formatter;
  }
  public function build($query, $ops) {
    if (!empty($ops["order"])) {
      $query->condition("shipping_lines.orders_id", $ops["order"]);
    }
    $query->select("shipping_lines.method.name as shipping_method");
    $query->select("shipping_lines.method.description as shipping_method_description");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $item) {
      $item['total'] = $item['price'] * $item['qty'];
      $item['total_formatted'] = $this->formatter->format($item['total']);
      $item['price_formatted'] = $this->formatter->format($item['price']);
      $rows[$idx] = $item;
    }
    return $rows;
  }
}
