<?php
namespace Starbug\Core;

class RegisterForm extends FormDisplay {
  public $model = "users";
  public $defaultAction = "register";
  public $submit_label = "Register";
  public function buildDisplay($options) {
    $this->request->setPost('users', 'password', null);
    $this->request->setPost('users', 'password_confirm', null);
    $this->add("email", "password", ["password_confirm", "input_type" => "password"]);
  }
}
