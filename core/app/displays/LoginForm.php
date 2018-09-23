<?php
namespace Starbug\Core;

class LoginForm extends FormDisplay {
  public $model = "users";
  public $default_action = "login";
  public $submit_label = "Login";
  public function build_display($options) {
    $this->request->setPost('users', 'password', null);
    $this->add("email", "password");
  }
}
