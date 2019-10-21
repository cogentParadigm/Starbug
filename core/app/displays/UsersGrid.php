<?php
namespace Starbug\Core;

class UsersGrid extends GridDisplay {
  public $model = "users";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add("first_name", "last_name", "email", "last_visit", "groups", ["deleted", "label" => "Status"]);
  }
}
