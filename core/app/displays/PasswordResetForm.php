<?php
namespace Starbug\Core;

class PasswordResetForm extends FormDisplay {
  public $model = "users";
  public $default_action = "reset_password";
  function build_display($options) {
    $this->add("email");
  }
}
