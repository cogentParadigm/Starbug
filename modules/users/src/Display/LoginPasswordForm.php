<?php
namespace Starbug\Users\Display;

use Starbug\Display\FormDisplay;

class LoginPasswordForm extends FormDisplay {
  public $model = "users";
  public $submit_label = "Login";
  public function buildDisplay($options) {
    $queryParams = $this->request->getQueryParams();
    $this->setPost('password', null);
    $this->add(["email", "default" => $options['email'] ?? $queryParams["email"] ?? ""]);
    $this->add(["password", "input_type" => "password"]);
  }
}
