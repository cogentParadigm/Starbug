<?php
namespace Starbug\Menus\Display;

use Starbug\Core\GridDisplay;

class MenusGrid extends GridDisplay {
  public $model = "menus";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("menu");
    $this->add(["row_options", "plugin" => "starbug.grid.columns.menu_options"]);
  }
}
