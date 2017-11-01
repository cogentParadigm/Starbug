<?php
namespace Starbug\Payment;

use Starbug\Core\AdminCollection;

class AdminPaymentGatewaySettingsCollection extends AdminCollection {
  public function build($query, &$ops) {
    $query = parent::build($query, $ops);
    if (!empty($ops["gateway"])) {
      $query->condition("payment_gateway_id", $ops["gateway"]);
    }
    return $query;
  }
}
