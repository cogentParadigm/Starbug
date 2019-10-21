<?php
namespace Starbug\Payment;

use Starbug\Core\SearchForm;

class OrdersSearchForm extends SearchForm {
  public function buildDisplay($options) {
    $this->attributes['class'][] = 'form-inline';
    $this->add(["keywords", "input_type" => "text", "nolabel"  => true]);
    $this->add([
      "order_status",
      "input_type" => "select",
      "multiple" => true,
      "nolabel" => true,
      "options" => ["Cart", "Pending", "Processing", "Completed"],
      "values" => ["cart", "pending", "processing", "completed"],
      "default" => ["pending"]
    ]);
    $this->actions->add(["search", "class" => "btn-default"]);
    $this->actions->template = "inline";
  }
}
