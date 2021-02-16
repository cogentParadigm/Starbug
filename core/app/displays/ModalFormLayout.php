<?php
namespace Starbug\Core;

use Starbug\Core\LayoutDisplay;
use Starbug\Core\Renderable;

class ModalFormLayout extends LayoutDisplay {
  public $template = "modal-form-layout.html";
  public function buildDisplay($options) {
    $this->put("div.modal-body", "", "body");
  }
  public function filter($field, $options) {
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
