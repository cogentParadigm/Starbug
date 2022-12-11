<?php
namespace Starbug\Core\Script;

use Psr\Container\ContainerInterface;

class ListScripts {
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }
  public function __invoke() {
    $names = array_filter($this->container->getKnownEntryNames(), function ($value) {
      return substr($value, 0, 8) == "scripts.";
    });
    $names = array_map(function ($value) {
      return substr($value, 8);
    }, $names);
    echo implode("\n", $names)."\n";
  }
}
