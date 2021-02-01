<?php
namespace Starbug\Core;

class DeleteForm extends FormDisplay {
  public $defaultAction = "delete";
  public $submit_label = "Delete";
  public function buildDisplay($options) {
    $this->actions->update([$this->defaultAction, "name" => "operation", "class" => "btn btn-danger"]);
  }
}
