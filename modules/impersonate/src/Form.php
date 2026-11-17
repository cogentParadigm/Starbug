<?php
namespace Starbug\Users\Impersonate;

use Starbug\Db\Collection\SelectCollection;
use Starbug\Display\FormDisplay;

class Form extends FormDisplay {
  public $collection = SelectCollection::class;
  public function buildDisplay($options) {
    $this->add(["label", "input_type" => "html", "value" => "<p>Are you sure you want to impersonate <em>".$this->get("label")."</em>?</p>"]);
    $this->actions->update([$this->defaultAction, "label" => "Impersonate", "name" => "operation", "class" => "btn btn-danger"]);
  }
}
