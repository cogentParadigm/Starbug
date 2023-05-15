<?php
namespace Starbug\Products\Admin\ProductTypes;

use Starbug\Core\GridDisplay;

class ProductTypesGrid extends GridDisplay {
  public $model = "product_types";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("name");
  }
}
