<?php
namespace Starbug\Imports;

use Starbug\Core\GridDisplay;

class ImportGroupsGrid extends GridDisplay {
  public $model = "import_groups";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add(["name", "readonly" => true]);
    $this->add(["created", "readonly" => true]);
    $this->add(["modified", "label" => "Last Modified", "readonly" => true]);
    $this->add(["row_options", "plugin" => "starbug.grid.columns.import_options"]);
  }
}
