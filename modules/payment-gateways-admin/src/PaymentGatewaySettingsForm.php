<?php
namespace Starbug\Payments\Gateways\Admin;

use Starbug\Core\FormDisplay;

class PaymentGatewaySettingsForm extends FormDisplay {
  public $model = "payment_gateway_settings";
  public function buildDisplay($options) {
    if (empty($options["id"])) {
      $this->add(["payment_gateway_id", "input_type" => "hidden", "default" => $this->request->getQueryParams()["gateway"]]);
    }
    $this->add("name");
    $this->add([
      "type",
      "input_type" => "select",
      "options" => "text,textarea,select,checkbox,radio,password",
      "data-dojo-type" => "starbug/form/Dependency",
      "data-dojo-props" => "key:'type'"
    ]);
    $this->add(["options", "input_type" => "textarea", "data-dojo-type" => "starbug/form/Dependent", "data-dojo-props" => "key:'type',values:['select']"]);
    $this->add(["test_mode_value", "input_type" => "textarea"]);
    $this->add(["live_mode_value", "input_type" => "textarea"]);
    $this->add(["description", "input_type" => "textarea"]);
  }
}
