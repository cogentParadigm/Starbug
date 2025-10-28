<?php
namespace Starbug\ShippingMethods\Admin;

use Starbug\Admin\Db\Query\AdminCollection;

class ShippingMethodsAdminCollection extends AdminCollection {
  public function build($query, $ops) {
    if (empty($ops["sort"])) {
      $ops["sort"] = "shipping_methods.position";
    }
    return parent::build($query, $ops);
  }
}
