<?php
namespace Starbug\Imports\Read;

use Starbug\Imports\Read\Traits\Transformers;

abstract class Strategy implements StrategyInterface {
  protected $currentRow;
  protected $fields = [];

  use Transformers;

  public function setFields($fields = []) {
    $this->fields = $fields;
  }

  public function getLocation($record) {
    return "row ".$this->currentRow;
  }
  protected function getMappedValues($source) {
    if (empty($this->fields)) {
      $mapped = $source;
    } else {
      $mapped = [];
      foreach ($this->fields as $field) {
        $treePath = explode(": ", $field["destination"]);
        $target = &$mapped;
        while (count($treePath) > 1) {
          $key = array_shift($treePath);
          if (!isset($target[$key])) {
            $target[$key] = [];
          }
          $target = &$target[$key];
        }
        $key = array_shift($treePath);
        $target[$key] = $source[$field["source"]];
      }
    }
    return $this->applyTransformers($source, $mapped);
  }
}
