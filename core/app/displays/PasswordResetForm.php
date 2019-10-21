<?php
namespace Starbug\Core;

class PasswordResetForm extends FormDisplay {
  public $model = "users";
  public $defaultAction = "resetPassword";
  public function buildDisplay($options) {
    $this->add("email");
  }
}
