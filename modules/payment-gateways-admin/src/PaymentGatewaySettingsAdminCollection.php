<?php
namespace Starbug\Payments\Gateways\Admin;

use Starbug\Admin\Db\Query\AdminCollection;

class PaymentGatewaySettingsAdminCollection extends AdminCollection {
  public function build($query, $ops) {
    $query = parent::build($query, $ops);
    if (!empty($ops["gateway"])) {
      $query->condition("payment_gateway_id", $ops["gateway"]);
    }
    return $query;
  }
}
