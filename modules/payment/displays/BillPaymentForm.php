<?php
namespace Starbug\Payment;
class BillPaymentForm extends PaymentForm {
	public $model = "subscriptions";
	public $collection = "BillPaymentForm";
	public $default_action = "payment";
	public $submit_label = "Submit Payment";
	function build_display($options) {
		parent::build_display($options);
		$this->remove("email");
		$this->remove("phone");
		$this->add(["bill", "input_type" => "hidden"]);
	}
}
