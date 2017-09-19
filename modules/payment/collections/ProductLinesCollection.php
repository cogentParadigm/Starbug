<?php
namespace Starbug\Payment;

use Starbug\Core\Collection;
use Starbug\Core\ModelFactoryInterface;

class ProductLinesCollection extends Collection {
  public function __construct(ModelFactoryInterface $models, PriceFormatterInterface $formatter) {
    $this->models = $models;
    $this->formatter = $formatter;
  }
  public function build($query, &$ops) {
    $query->condition("product_lines.orders_id", $ops["order"]);
    $query->select("product_lines.product.sku");
    $query->select("product_lines.product.type.slug as product_type");
    $query->select("product_lines.orders_id.order_status as order_status");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $item) {
      $item['description'] = (string) '<strong>'.$item['description'].'</strong>';
      $item['total'] = $item['price'] * $item['qty'];
      $item['total_formatted'] = $this->formatter->format($item['total']);
      $item['price_formatted'] = $this->formatter->format($item['price']);
      if ($item["recurring"]) {
        $unit = $item["unit"];
        $interval = $item["interval"];
        $phrase = "";
        if ($unit == "days" && $interval == 365) {
          $phrase = "year";
        } else if ($interval == 1) {
          $phrase = substr($unit, 0, 1);
        } else {
          $phrase = $interval." ".$unit;
        }
        $item["price_formatted"] .= "/".$phrase;
        $item["total_formatted"] .= "/".$phrase;
      }
      $rows[$idx] = $item;
    }
    return $rows;
  }
}
