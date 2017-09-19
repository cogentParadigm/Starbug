<?php
namespace Starbug\Payment;

use Starbug\Core\GridDisplay;

class PaymentGatewaysGrid extends GridDisplay {
  public $model = "payment_gateways";
  public $action = "admin";
  function build_display($options) {
    $this->add("name");
    $this->add("description");
    $this->add("is_active");
    $this->add("is_test_mode");
    $this->add(["row_options", "plugin" => "payment.grid.columns.payment_gateway_options"]);
  }
}
