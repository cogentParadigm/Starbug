<?php
namespace Starbug\Intl\Display;

use Starbug\Core\GridDisplay;

class CountriesGrid extends GridDisplay {
  public $model = "countries";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("name");
    $this->add("code");
  }
}
