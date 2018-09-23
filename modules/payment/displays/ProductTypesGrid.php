<?php
namespace Starbug\Payment;

use Starbug\Core\GridDisplay;

class ProductTypesGrid extends GridDisplay {
  public $model = "product_types";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("name");
  }
}
