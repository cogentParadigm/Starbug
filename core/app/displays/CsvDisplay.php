<?php
namespace Starbug\Core;

class CsvDisplay extends ItemDisplay {
  public $template = "csv";
  public function buildDisplay($options) {
    $this->model = $options['model'];
    $this->action = $options['action'];
    $this->models->get($this->model)->buildDisplay($this);
  }
  public function query($options = null) {
    if (isset($this->options['data'])) $this->items = $this->options['data'];
    else parent::query($options);
  }
}
