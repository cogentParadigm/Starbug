<?php
namespace Starbug\Core;

class CsvDisplay extends ItemDisplay {
  public $template = "csv";
  public function __construct(TemplateInterface $output, CollectionFactoryInterface $collections) {
    $this->output = $output;
    $this->collections = $collections;
  }
  public function buildDisplay($options) {
    $this->model = $options['model'];
    $this->action = $options['action'];
  }
  public function query($options = null) {
    if (isset($this->options['data'])) $this->items = $this->options['data'];
    else parent::query($options);
  }
}
