<?php
namespace Starbug\Content;

use Starbug\Core\GridDisplay;

class TagsGrid extends GridDisplay {
  public $model = "tags";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->dnd();
    $this->insert(0, ["id", "plugin" => "starbug.grid.columns.tree", "sortable" => "false"]);
    $this->add(["title", "sortable" => "false"]);
    $this->add(["position", "sortable" => "false"]);
  }
}
