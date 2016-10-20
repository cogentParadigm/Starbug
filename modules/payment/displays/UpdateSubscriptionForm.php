<?php
namespace Starbug\Payment;
class UpdateSubscriptionForm extends PaymentForm {
	public $model = "subscriptions";
	public $collection = "Form";
	public $default_action = "update";
	public $submit_label = "Update";
	function build_display($options) {
		parent::build_display($options);
		$this->remove("email");
		$this->remove("phone");
	}
}
?>
