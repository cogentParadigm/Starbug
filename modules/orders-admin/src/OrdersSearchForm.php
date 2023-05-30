<?php
namespace Starbug\Orders\Admin;

use Starbug\Core\SearchForm;

class OrdersSearchForm extends SearchForm {
  protected function buildPrimaryControls() {
    parent::buildPrimaryControls();
    $this->add([
      "order_status",
      "nolabel" => true,
      "data-dojo-type" => "sb/form/MultipleSelect",
      "data-dojo-props" => "collection:new (require('dstore/Memory'))({data:[{id:'cart', label:'Cart'}, {id:'pending', label:'Pending'}, {id:'processing', label:'Processing'}, {id:'completed', label:'Completed'}]})",
      "default" => "pending"
    ]);
  }
  public function buildDisplay($options) {
    parent::buildDisplay($options);
    /*
    $this->add([
      "order_status",
      "input_type" => "select",
      "multiple" => true,
      "nolabel" => true,
      "options" => ["Cart", "Pending", "Processing", "Completed"],
      "values" => ["cart", "pending", "processing", "completed"],
      "default" => ["pending"]
    ]);
    */
  }
}
