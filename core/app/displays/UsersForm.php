<?php
namespace Starbug\Core;

class UsersForm extends FormDisplay {
  public $model = "users";
  public $cancel_url = "admin/users";
  public function buildDisplay($options) {
    $this->layout->add(["top", "left" => "div.col-md-6.col-6", "right" => "div.col-md-6.col-6"]);
    $this->layout->put('left', 'h2.f8', 'User Information');
    $this->layout->put('right', 'h2.f8', 'Login Credentials');
    $this->add(["first_name", "pane" => "left"]);
    $this->add(["last_name", "pane" => "left"]);
    $this->add(["email", "pane" => "right"]);
    $this->add(["password", "input_type" => "password", "pane" => "right"]);
    $this->add(["password_confirm", "input_type" => "password", "pane" => "right"]);
    $this->add(["groups", "input_type" => "multiple_category_select", "taxonomy" => "groups", "pane" => "right", "query" => ["exclude" => "user"]]);
    $this->add(["deleted", "label" => "Status", "input_type" => "select", "values" => ["0", "1"], "options" => ["Active", "Deleted"], "pane" => "right"]);
  }
}
