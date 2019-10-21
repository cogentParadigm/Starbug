<?php
namespace Starbug\Payment;

class BillPaymentForm extends PaymentForm {
  public $model = "subscriptions";
  public $collection = "BillPaymentForm";
  public $defaultAction = "payment";
  public $submit_label = "Submit Payment";
  public function buildDisplay($options) {
    parent::buildDisplay($options);
    $this->remove("email");
    $this->remove("phone");
    $this->add(["bill", "input_type" => "hidden"]);
  }
}
