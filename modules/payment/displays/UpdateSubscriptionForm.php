<?php
namespace Starbug\Payment;

class UpdateSubscriptionForm extends PaymentForm {
  public $model = "subscriptions";
  public $collection = "Form";
  public $defaultAction = "update";
  public $submit_label = "Update";
  public function buildDisplay($options) {
    parent::buildDisplay($options);
    $this->remove("email");
    $this->remove("phone");
  }
}
