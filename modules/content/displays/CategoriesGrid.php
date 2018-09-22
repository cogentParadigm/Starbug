<?php
namespace Starbug\Content;

use Starbug\Core\GridDisplay;

class CategoriesGrid extends GridDisplay {
  public $model = "categories";
  public $action = "admin";
  public function build_display($options) {
    $this->dnd();
    $this->insert(0, ["id", "plugin" => "starbug.grid.columns.tree", "sortable" => "false"]);
    $this->add(["name", "sortable" => "false"]);
    $this->add(["position", "sortable" => "false"]);
  }
}
