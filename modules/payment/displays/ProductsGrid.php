<?php
namespace Starbug\Payment;

use Starbug\Core\GridDisplay;

class ProductsGrid extends GridDisplay {
  public $model = "products";
  public $action = "admin";
  public function build_display($options) {
    $this->add("type");
    $this->add("sku");
    $this->add("name");
  }
}
