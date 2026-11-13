<?php
namespace Starbug\Intl\Display;

use Starbug\Dojo\Display\GridDisplay;

class ProvincesGrid extends GridDisplay {
  public $model = "provinces";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("name");
    $this->add("code");
  }
}
