<?php
namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class PaymentForm extends FormDisplay {
  public $model = "orders";
  public $collection = "OrdersForm";
  public $defaultAction = "payment";
  public $submit_label = "Place Order";
  public function buildDisplay($options) {
    $this->add(["email", "input_type" => "text"]);
    $this->add(["phone", "label" => "Phone Number", "input_type" => "text"]);
    $this->add([
      "card",
      "label" => "Existing credit and debit cards",
      "input_type" => "select",
      "from" => "payment_cards",
      "query" => "SelectPaymentCard",
      "required" => false,
      "data-dojo-type" => "starbug/form/Dependency",
      "data-dojo-props" => "key:'card'"
    ]);
    $dep = ["data-dojo-type" => "starbug/form/Dependent", "data-dojo-props" => "key:'card',values:['']"];
    $this->add(["card_number", "input_type" => "text"] + $dep);
    $this->add(["card_holder", "label" => "Name on card", "input_type" => "text"] + $dep);
    $this->add(["expiration_date", "label" => "Expiration Date", "input_type" => "text", "placeholder" => "MM/YYYY"] + $dep);
    $this->add(["cvv", "label" => "Security code", "input_type" => "text"] + $dep);
    $this->actions->add([$this->defaultAction, "class" => "btn-primary"]);
  }
}
