<?php
namespace Starbug\Core;

class DeleteForm extends FormDisplay {
  public $defaultAction = "delete";
  public $submit_label = "Delete";
  public $collection = "Select";
  public function buildDisplay($options) {
    $this->cancel_url = preg_replace("/\/delete\/.*/", "", $this->request->getUri()->getPath());
    $this->add(["label", "input_type" => "html", "value" => "<p>Are you sure you want to delete <em>".$this->get("label")."</em>?</p>"]);
    $this->actions->update([$this->defaultAction, "name" => "operation", "class" => "btn btn-danger"]);
  }
}
