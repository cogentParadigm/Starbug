<?php
namespace Starbug\Products\Admin\Products;

use Starbug\Core\GridDisplay;

class ProductsGrid extends GridDisplay {
  public $model = "products";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("type");
    $this->add("sku");
    $this->add("name");
  }
}
