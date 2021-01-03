<?php
namespace Starbug\Users;

use Starbug\Core\FormDisplay;

class PasswordResetForm extends FormDisplay {
  public $model = "users";
  public $defaultAction = "resetPassword";
  public $submit_label = "Reset Password";
  public function buildDisplay($options) {
    $this->add(["token", "input_type" => "hidden", "default" => $this->request->getQueryParams()['token']]);
    $this->add(["email", "input_type" => "text"]);
    $this->add(["password", "input_type" => "password"]);
    $this->add(["password_confirm", "input_type" => "password"]);
  }
}
