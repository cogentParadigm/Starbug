<?php
namespace Starbug\Payment;

use Starbug\Core\GridDisplay;

class ProductTypesGrid extends GridDisplay {
  public $model = "product_types";
  public $action = "admin";
  public function build_display($options) {
    $this->add("name");
  }
}
