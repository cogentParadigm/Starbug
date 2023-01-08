<?php
namespace Starbug\Imports\Read;

use Iterator;
use Starbug\Core\CollectionFactoryInterface;
use Starbug\Imports\Import;

class CollectionStrategy extends Strategy {
  public function __construct(CollectionFactoryInterface $collections) {
    $this->collections = $collections;
  }
  public function getRows($options = []) : Iterator {
    $collection = $options["collection"];
    $options = $this->getDefaultOptions($options);
    $collection = $this->collections->get($collection);
    $pager = false;
    do {
      $records = $collection->query($options);
      foreach ($records as $idx => $source) {
        $this->currentRow = $idx;
        $source = (array) $source;
        $dest = $this->getMappedValues($source);
        yield $dest;
      }
      $options["page"]++;
      $pager = $collection->getPager();
    } while ($pager && $options["page"] <= $pager->last);
  }
  protected function getDefaultOptions($options = []) {
    if (empty($options["limit"]) && empty($options["nolimit"])) {
      $options["limit"] = 1000;
    }
    if (empty($options["page"])) {
      $options["page"] = 1;
    }
    return $options;
  }
}
