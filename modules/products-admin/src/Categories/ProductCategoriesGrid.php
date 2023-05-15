<?php
namespace Starbug\Products\Admin\Categories;

use Starbug\Core\GridDisplay;

class ProductCategoriesGrid extends GridDisplay {
  public $model = "product_categories";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->dnd();
    $this->insert(0, ["id", "plugin" => "starbug.grid.columns.tree", "sortable" => "false"]);
    $this->add(["name", "sortable" => "false"]);
    $this->add(["position", "sortable" => "false"]);
  }
}
