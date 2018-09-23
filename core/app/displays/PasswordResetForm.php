<?php
namespace Starbug\Core;

class PasswordResetForm extends FormDisplay {
  public $model = "users";
  public $defaultAction = "reset_password";
  public function buildDisplay($options) {
    $this->add("email");
  }
}
