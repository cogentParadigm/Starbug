<?php
namespace Starbug\Payment;
use Starbug\Core\GridDisplay;
class PaymentGatewaySettingsGrid extends GridDisplay {
	public $model = "payment_gateway_settings";
	public $action = "admin";
	function build_display($options) {
		$this->add("name");
		$this->add("description");
	}
}
