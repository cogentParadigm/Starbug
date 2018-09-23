<?php
namespace Starbug\Core;

class LoginForm extends FormDisplay {
  public $model = "users";
  public $defaultAction = "login";
  public $submit_label = "Login";
  public function buildDisplay($options) {
    $this->request->setPost('users', 'password', null);
    $this->add("email", "password");
  }
}
