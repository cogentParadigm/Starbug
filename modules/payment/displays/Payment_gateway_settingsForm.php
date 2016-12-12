<?php
namespace Starbug\Payment;
use Starbug\Core\FormDisplay;
class Payment_gateway_settingsForm extends FormDisplay {
	public $model = "payment_gateway_settings";
	public $cancel_url = "admin/payment_gateway_settings";
	function build_display($options) {
		$this->add(["payment_gateway_id", "input_type" => "hidden", "default" => $this->request->getParameter("gateway")]);
		$this->add("name");
		$this->add([
			"type",
			"input_type" => "select",
			"options" => "text,textarea,select,checkbox,radio,password,file_select",
			"data-dojo-type" => "starbug/form/Dependency",
			"data-dojo-props" => "key:'type'"
		]);
		$this->add(["options", "data-dojo-type" => "starbug/form/Dependent", "data-dojo-props" => "key:'type',values:['select']"]);
		$this->add("test_mode_value");
		$this->add("live_mode_value");
		$this->add("description");
	}
}
