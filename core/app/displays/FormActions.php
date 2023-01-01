<?php
namespace Starbug\Core;

class FormActions extends ItemDisplay {
  protected $modal = false;
  public function buildDisplay($options) {
    if (!empty($options["modal"])) {
      $this->attributes["class"] = "modal-footer";
    }
    if (!empty($options["defaultAction"])) {
      $action = [$options["defaultAction"], "template" => "button/primary.html"];
      if (!empty($options["defaultActionLabel"])) {
        $action["label"] = $options["defaultActionLabel"];
      }
      $this->add($action);
    }
  }
}
