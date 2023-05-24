<?php
namespace Starbug\ShippingMethods\Admin;

use Starbug\Core\FormDisplay;

class ShippingMethodsForm extends FormDisplay {
  public $model = "shipping_methods";
  public function buildDisplay($options) {
    $this->add("name");
    $this->add(["description", "input_type" => "textarea"]);
    $this->add(["shipping_rates", "input_type" => "text", "data-dojo-type" => "sb/form/CRUDList", "data-dojo-props" => "model:'shipping_rates', newItemLabel:'Add New Shipping Rate', dialogParams: { title: 'New Shipping Rate'}"]);
    $this->add("position");
  }
}
