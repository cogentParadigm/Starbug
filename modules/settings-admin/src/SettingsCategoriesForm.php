<?php
namespace Starbug\Settings\Admin;

use Starbug\Core\FormDisplay;

class SettingsCategoriesForm extends FormDisplay {
  public $model = "settings_categories";
  public function buildDisplay($options) {
    $this->add(["name", "required" => true]);
    $this->add(["slug"]);
    $this->add(["position"]);
  }
}
