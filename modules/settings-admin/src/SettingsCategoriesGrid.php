<?php
namespace Starbug\Settings\Admin;

use Starbug\Dojo\Display\GridDisplay;

class SettingsCategoriesGrid extends GridDisplay {
  public $model = "settings_categories";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->dnd();
    $this->add(["name"]);
  }
}
