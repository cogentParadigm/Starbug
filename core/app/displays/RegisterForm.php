<?php
namespace Starbug\Core;

class RegisterForm extends FormDisplay {
  public $model = "users";
  public $default_action = "register";
  public $submit_label = "Register";
  function build_display($options) {
    $this->request->setPost('users', 'password', null);
    $this->request->setPost('users', 'password_confirm', null);
    $this->add("email", "password", ["password_confirm", "input_type" => "password"]);
  }
}
