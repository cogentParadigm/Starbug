<?php
namespace Starbug\Payment;

use Starbug\Core\Collection;
use Starbug\Core\ModelFactoryInterface;

class ProductLinesCollection extends Collection {
  protected $model = "product_lines";
  public function __construct(ModelFactoryInterface $models, PriceFormatterInterface $formatter) {
    $this->models = $models;
    $this->formatter = $formatter;
  }
  public function build($query, $ops) {
    if (!empty($ops["order"])) {
      $query->condition("product_lines.orders_id", $ops["order"]);
    }
    $query->select("product_lines.product.sku");
    $query->select("product_lines.product.type.slug as product_type");
    $query->select("product_lines.orders_id.order_status as order_status");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $item) {
      $options = $this->models->get("product_lines_options")->query()
        ->select("options_id.slug")->condition("product_lines_id", $item["id"])->all();
      foreach ($options as $option) {
        $item["options"][$option["slug"]] = $option["value"];
      }
      $item['total'] = $item['price'] * $item['qty'];
      $item['total_formatted'] = $this->formatter->format($item['total']);
      $item['price_formatted'] = $this->formatter->format($item['price']);
      if (!empty($item["recurring"])) {
        $unit = $item["unit"];
        $interval = $item["interval"];
        $phrase = "";
        if ($unit == "days" && $interval == 365) {
          $phrase = "year";
        } elseif ($interval == 1) {
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
