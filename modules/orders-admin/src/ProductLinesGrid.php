<?php
namespace Starbug\Orders\Admin;

use Starbug\Core\GridDisplay;

class ProductLinesGrid extends GridDisplay {
  public $model = "product_lines";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add(["description", "plugin" => "starbug.grid.columns.html", "label" => "Product"]);
    $this->add(["price_formatted", "label" => "Price"]);
    $this->add(["qty", "label" => "Quantity"]);
    $this->add(["total_formatted", "label" => "Total"]);
    $this->remove("row_options");
  }
}
