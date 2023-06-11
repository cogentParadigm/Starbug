<?php
namespace Starbug\Imports\Read;

use Iterator;

class YamlFixtureStrategy extends FileStrategy {
  protected $currentTable;
  public function getRows($options = []) : Iterator {
    $tables = yaml_parse_file($this->path);
    foreach ($tables as $name => $rows) {
      $this->currentTable = $name;
      $rows = $rows ?? [];
      foreach ($rows as $idx => $source) {
        $this->currentRow = $idx + 1;
        $rows[$idx] = $this->getMappedValues($source);
      }
      yield ["table" => $name, "rows" => $rows];
    }
  }
  public function getLocation($record) {
    return $this->currentTable." row ".$this->currentRow;
  }
}
