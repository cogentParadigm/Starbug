<?php
namespace Starbug\Core;

class ProfileForm extends FormDisplay {
  public $model = "users";
  public $defaultAction = "updateProfile";
  public function buildDisplay($options) {
    $this->setPost('current_password', null);
    $this->setPost('password', null);
    $this->setPost('password_confirm', null);
    $this->add(["current_password", "input_type" => "password", "label" => "Current Password", "info" => "To change your email address or password, you must enter your current password."]);
    $this->add("email");
    $this->add(["password", "label" => "New Password", "required" => "false"]);
    $this->add(["password_confirm", "input_type" => "password", "label" => "Confirm New Password", "required" => "false"]);
  }
}
