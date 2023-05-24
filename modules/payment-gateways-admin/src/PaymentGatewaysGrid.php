<?php
namespace Starbug\Payments\Gateways\Admin;

use Starbug\Core\GridDisplay;

class PaymentGatewaysGrid extends GridDisplay {
  public $model = "payment_gateways";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("name");
    $this->add("description");
    $this->add("is_active");
    $this->add("is_test_mode");
    $this->add([
      "row_options",
      "buttons" => "[".
        "{".
          "url: dojoConfig.websiteUrl + 'admin/payment-gateways/settings/\${id}', ".
          "title: 'Settings', ".
          "icon: 'fa-cogs'".
        "}".
      "]"
    ]);
  }
}
