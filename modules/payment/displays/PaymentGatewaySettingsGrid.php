<?php
namespace Starbug\Payment;

use Starbug\Core\GridDisplay;

class PaymentGatewaySettingsGrid extends GridDisplay {
  public $model = "payment_gateway_settings";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->attr("base_url", "admin/payment-gateway-settings");
    $this->add("name");
    $this->add("description");
  }
}
