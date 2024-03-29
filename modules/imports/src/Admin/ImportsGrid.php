<?php
namespace Starbug\Imports\Admin;

use Starbug\Core\GridDisplay;

class ImportsGrid extends GridDisplay {
  public $model = "imports";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->attr("base_url", "admin/imports");
    $this->add(["name", "readonly" => true]);
    if (empty($options['model'])) {
      $this->add(["model", "readonly" => true]);
    }
    $this->add(["created", "readonly" => true]);
    $this->add(["modified", "label" => "Last Modified", "readonly" => true]);
    $this->add(["row_options", "plugin" => "starbug.grid.columns.import_options"]);
  }
}
