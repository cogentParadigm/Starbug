<?php
namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class PaymentForm extends FormDisplay {
  public $model = "orders";
  public $collection = "OrdersForm";
  public $defaultAction = "payment";
  public $submit_label = "Place Order";
  public function buildDisplay($options) {
    $this->layout->add(["a", "top" => "div", "middle" => "div", "bottom" => "div"]);
    $this->add(["email", "input_type" => "text", "pane" => "top", "div" => "col-xs-12"]);
    $this->add(["phone", "label" => "Phone Number", "input_type" => "text", "pane" => "top", "div" => "col-xs-12"]);
    $this->add([
      "card",
      "label" => "Existing credit and debit cards",
      "input_type" => "select",
      "pane" => "top",
      "div" => "col-xs-12",
      "from" => "payment_cards",
      "query" => "SelectPaymentCard",
      "required" => false,
      "data-dojo-type" => "starbug/form/Dependency",
      "data-dojo-props" => "key:'card'"
    ]);
    $dep = ["data-dojo-type" => "starbug/form/Dependent", "data-dojo-props" => "key:'card',values:['']"];
    $this->add(["card_number", "input_type" => "text", "pane" => "top", "div" => "col-xs-12"] + $dep);
    $this->add(["card_holder", "label" => "Name on card", "input_type" => "text", "pane" => "top", "div" => "col-xs-12"] + $dep);
    $this->add(["expiration_date[month]", "pane" => "middle", "div" => "col-xs-6", "label" => "Expiration Month", "input_type" => "select", "range" => "1-12"] + $dep);
    $this->add(["expiration_date[year]", "pane" => "middle", "div" => "col-xs-6", "label" => "Expiration Year", "input_type" => "select", "range" => date("Y")."-".(intval(date("Y"))+20)] + $dep);
    $this->add(["cvv", "label" => "Security code", "pane" => "bottom", "div" => "col-xs-12", "input_type" => "text"] + $dep);
    $this->actions->add([$this->defaultAction, "class" => "btn-primary"]);
  }
}
