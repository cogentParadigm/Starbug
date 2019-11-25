<?php
namespace Starbug\Users;

use Starbug\Core\FormDisplay;

class ForgotPasswordForm extends FormDisplay {
  public $model = "users";
  public $defaultAction = "forgotPassword";
  public $submit_label = "Forgot Password";
  public function buildDisplay($options) {
    $this->add("email");
  }
}
