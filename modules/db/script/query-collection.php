<?php
namespace Starbug\Db;

use Starbug\Core\CollectionFactoryInterface;

class QueryCollectionCommand {
  public function __construct(CollectionFactoryInterface $collections) {
    $this->collections = $collections;
  }
  public function run($argv) {
    $positional = [];
    $named = [];
    foreach ($argv as $i => $arg) {
      if (0 === strpos($arg, "-")) {
        $arg = ltrim($arg, "-");
        $parts = (false !== strpos($arg, "=")) ? explode("=", $arg, 2) : [$arg, true];
        $named[$parts[0]] = $parts[1];
      } else {
        $positional[] = $arg;
      }
    }
    $options = $named;
    $collection = array_shift($positional);
    $results = $this->collections->get($collection)->query($options);
    echo json_encode($results, JSON_PRETTY_PRINT);
  }
}
