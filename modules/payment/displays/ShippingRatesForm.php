<?php

namespace Starbug\Payment;

use Starbug\Core\FormDisplay;

class ShippingRatesForm extends FormDisplay {
  public $model = "shipping_rates";
  public $cancel_url = "admin/shipping_rates";
  public function buildDisplay($options) {
    if ($this->success("create") && !$this->hasPost("id")) $this->setPost("id", $this->db->getInsertId($this->model));
    $this->layout->add(["row", "left" => "div.col-sm-8", "right" => "div.col-sm-4"]);
    // $this->add(["additive", "info" => "Check to make this an add-on rather than the base rate.", "pane" => "left"]);
    $this->add(["name", "pane" => "left"]);
    $this->add(["price", "info" => "Enter price in cents. For example, enter 5000 for $50.", "pane" => "left"]);
    $this->add(["product_types", "pane" => "right"]);
    $this->add(["product_options", "pane" => "right", "input_type" => "text", "data-dojo-type" => "sb/form/CRUDList", "data-dojo-props" => "model:'shipping_rates_product_options', newItemLabel:'Add Product Option Condition'"]);
  }
}
