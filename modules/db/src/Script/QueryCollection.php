<?php
namespace Starbug\Db\Script;

use Starbug\Db\CollectionFactoryInterface;

class QueryCollection {
  public function __construct(CollectionFactoryInterface $collections) {
    $this->collections = $collections;
  }
  public function __invoke($positional, $named) {
    $options = $named;
    $collection = array_shift($positional);
    $results = $this->collections->get($collection)->query($options);
    echo yaml_emit($results);
  }
}
