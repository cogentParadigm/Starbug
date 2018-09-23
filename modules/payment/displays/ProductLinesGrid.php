<?php
namespace Starbug\Payment;

use Starbug\Core\GridDisplay;

class ProductLinesGrid extends GridDisplay {
  public $model = "product_lines";
  public $action = "order";
  public function build_display($options) {
    $this->add(["description", "plugin" => "starbug.grid.columns.html", "label" => "Product"]);
    $this->add(["price_formatted", "label" => "Price"]);
    $this->add(["qty", "label" => "Quantity"]);
    $this->add(["total_formatted", "label" => "Total"]);
    // $this->add(["row_options", "plugin" => "payment.grid.columns.lines"]);
    $this->remove("row_options");
  }
}
