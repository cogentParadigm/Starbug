<?php
namespace Starbug\Emails\Admin;

use Starbug\Dojo\Display\GridDisplay;

class EmailTemplatesGrid extends GridDisplay {
  public $model = "email_templates";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("name");
  }
}
