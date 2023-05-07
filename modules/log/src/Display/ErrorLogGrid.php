<?php
namespace Starbug\Log\Display;

use Starbug\Core\GridDisplay;

class ErrorLogGrid extends GridDisplay {
  public $model = "error_log";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("channel");
    $this->add("level");
    $this->add(["message", "plugin" => "starbug.grid.columns.html", "sortable" => false]);
    $this->add("time");
    $this->remove("row_options");
  }
}
