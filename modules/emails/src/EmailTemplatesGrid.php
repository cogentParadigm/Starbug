<?php
namespace Starbug\Emails;

use Starbug\Core\GridDisplay;

class EmailTemplatesGrid extends GridDisplay {
  public $model = "email_templates";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("name");
  }
}
