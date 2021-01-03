<?php
namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class CheckoutForm extends FormDisplay {
  public $model = "orders";
  public $collection = "OrdersForm";
  public $defaultAction = "checkout";
  public $submit_label = "Contintue to Payment";
  public function buildDisplay($options) {
    $this->layout->add(["a", "left" => "div.col-l", "right" => "div.col-l"]);
    $this->add([
      "shipping_panel_top",
      "pane" => "left",
      "input_type" => "html",
      "value" => '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Shipping Address</h3></div><div class="panel-body">'
    ]);
    $this->add(["shipping_address", "pane" => "left", "input_type" => "text", "nolabel" => "", "data-dojo-type" => "sb/form/AddressSelect", "data-dojo-props" => "dialogParams:{formData:{'address[order_token]':'".$this->request->getCookieParams()["cid"]."'}}"]);
    $this->add([
      "shipping_panel_bottom",
      "pane" => "left",
      "input_type" => "html",
      "value" => '</div></div>'
    ]);
    $this->add([
      "billing_panel_top",
      "pane" => "right",
      "input_type" => "html",
      "value" => '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">Billing Address</h3></div><div class="panel-body">'
    ]);
    $this->add([
      "billing_same",
      "input_type" => "checkbox",
      "value" => "1",
      "label" => "My billing address is the same as my shipping address",
      "pane" => "right",
      "required" => false,
      "data-dojo-type" => "starbug/form/Checkbox",
      "data-dojo-mixins" => "starbug/form/Dependency",
      "data-dojo-props" => "key:'billing_same'"
    ]);
    $this->add([
      "billing_address",
      "pane" => "right",
      "input_type" => "text",
      "nolabel" => "",
      "data-dojo-type" => "sb/form/AddressSelect",
      "data-dojo-mixins" => "starbug/form/Dependent",
      "data-dojo-props" => "key:'billing_same',values:[0],dialogParams:{formData:{'address[order_token]':'".$this->request->getCookieParams()["cid"]."'}}"
    ]);
    $this->add([
      "billing_panel_bottom",
      "pane" => "right",
      "input_type" => "html",
      "value" => '</div></div>'
    ]);
    $this->actions->add([$this->defaultAction, "class" => "btn-primary"]);
  }
}
