<?php
namespace Starbug\Core;

use Starbug\Core\LayoutDisplay;
use Starbug\Core\Renderable;

class FormLayout extends LayoutDisplay {
  protected $modal = false;
  public function buildDisplay($options) {
    if (!empty($options["modal"])) {
      $this->modal = true;
      $this->template = "modal-form-layout.html";
      $this->put("div.modal-body", "", "body");
    }
  }
  public function filter($field, $options) {
    if (!$this->modal) {
      return parent::filter($field, $options);
    }
    $row = Renderable::create($this->cells["body"], "div.row");
    foreach ($options as $k => $v) {
      if ($k !== 'attributes') {
        $this->cells[$k] = Renderable::create($row, $v);
        $this->lastCell = $k;
      }
    }
    return $options;
  }
}
