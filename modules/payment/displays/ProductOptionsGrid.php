<?php

namespace Starbug\Payment;

use Starbug\Core\GridDisplay;

class ProductOptionsGrid extends GridDisplay {
  public $model = "product_options";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->dnd();
    $this->insert(0, ["tree", "field" => "id", "plugin" => "starbug.grid.columns.tree", "sortable" => false]);
    $this->add(["id", "label" => "ID"]);
    $this->add("name");
    $this->add("type");
    $this->add("position");
  }
}
