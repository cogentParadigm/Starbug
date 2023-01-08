<?php
namespace Starbug\Imports\Read;

use Iterator;
use Starbug\Imports\Import;

class YamlStrategy extends FileStrategy {
  public function getRows($options = []) : Iterator {
    $rows = yaml_parse_file($this->path);
    foreach ($rows as $idx => $source) {
      $this->currentRow = $idx + 1;
      $dest = $this->getMappedValues($source);
      yield $dest;
    }
  }
}
