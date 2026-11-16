<?php
namespace Starbug\Users\Display;

use Starbug\Display\FormDisplay;

class CheckLoginMethodForm extends FormDisplay {
  public $model = "users";
  public $submit_label = "Continue";
  public function buildDisplay($options) {
    $this->add(["email"]);
  }
}
