<?php
namespace Starbug\Payment;

use Starbug\Core\AdminCollection;

class AdminShippingMethodsCollection extends AdminCollection {
  public function build($query, $ops) {
    if (empty($ops["sort"])) {
      $ops["sort"] = "shipping_methods.position";
    }
    return parent::build($query, $ops);
  }
}
