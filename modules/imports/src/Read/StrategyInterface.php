<?php
namespace Starbug\Imports\Read;

use Iterator;
use Starbug\Imports\Import;

interface StrategyInterface {
  public function getRows(Import $import, $options = []) : Iterator;
  public function getLocation($record);
}
