<?php

namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class ShippingRatesForm extends FormDisplay {
  public $model = "shipping_rates";
  public function buildDisplay($options) {
    if ($this->success("create") && !$this->hasPost("id")) {
      $this->setPost("id", $this->db->getInsertId($this->model));
    }
    // $this->add(["additive", "info" => "Check to make this an add-on rather than the base rate."]);
    $this->add(["name"]);
    $this->add(["price", "info" => "Enter price in cents. For example, enter 5000 for $50."]);
    $this->add(["product_types", "input_type" => "multiple_select", "from" => "product_types", "query" => "Select"]);
    $this->add(["product_options", "input_type" => "text", "data-dojo-type" => "sb/form/CRUDList", "data-dojo-props" => "model:'shipping_rates_product_options', newItemLabel:'Add Product Option Condition'"]);
  }
}
