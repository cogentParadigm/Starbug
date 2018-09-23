<?php
namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class PaymentGatewaysForm extends FormDisplay {
  public $model = "payment_gateways";
  public $cancel_url = "admin/payment_gateways";
  public function build_display($options) {
    $this->add("name");
    $this->add("description");
    $this->add("is_active");
    $this->add("is_test_mode");
  }
}
