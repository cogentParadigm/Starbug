<?php

namespace Starbug\Payment;

use Starbug\Core\GridDisplay;

class ShippingMethodsGrid extends GridDisplay {
  public $model = "shipping_methods";
  public $action = "admin";
  public function build_display($options) {
    $this->dnd();
    $this->add("position");
    $this->add("name");
    $this->add("description");
  }
}
