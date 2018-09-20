<?php
namespace Starbug\Intl;

use Starbug\Core\GridDisplay;

class ProvincesGrid extends GridDisplay {
  public $model = "provinces";
  public $action = "admin";
  public function build_display($options) {
    $this->add("name");
    $this->add("code");
  }
}
