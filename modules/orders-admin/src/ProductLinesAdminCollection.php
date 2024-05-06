<?php
namespace Starbug\Orders\Admin;

use Starbug\Db\Collection;
use Starbug\Db\DatabaseInterface;
use Starbug\Price\FormatterInterface;

class ProductLinesAdminCollection extends Collection {
  protected $model = "product_lines";
  public function __construct(
    protected DatabaseInterface $db,
    protected FormatterInterface $formatter
  ) {
  }
  public function build($query, $ops) {
    $query->innerJoin("lines")->on("lines.id=product_lines.lines_id");
    if (!empty($ops["order"])) {
      $query->condition("lines.orders_id", $ops["order"]);
    }
    $query->select("lines.*");
    $query->select("product_lines.*");
    return $query;
  }
  public function filterRows($rows) {
    foreach ($rows as $idx => $item) {
      $item["total"] = $item["price"] * $item["qty"];
      $item["total_formatted"] = $this->formatter->format($item["total"]);
      $item["price_formatted"] = $this->formatter->format($item["price"]);
      $rows[$idx] = $item;
    }
    return $rows;
  }
}
