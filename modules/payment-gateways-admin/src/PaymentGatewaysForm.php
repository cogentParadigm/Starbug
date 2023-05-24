<?php
namespace Starbug\Payments\Gateways\Admin;

use Starbug\Core\FormDisplay;

class PaymentGatewaysForm extends FormDisplay {
  public $model = "payment_gateways";
  public function buildDisplay($options) {
    $this->add(["name", "input_type" => "text"]);
    $this->add(["description", "input_type" => "textarea"]);
    $this->add(["is_active", "input_type" => "checkbox", "value" => 1]);
    $this->add(["is_test_mode", "input_type" => "checkbox", "value" => 1]);
  }
}
