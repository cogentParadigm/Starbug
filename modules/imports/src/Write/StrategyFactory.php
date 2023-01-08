<?php
namespace Starbug\Imports\Write;

use DI\FactoryInterface;

class StrategyFactory implements StrategyFactoryInterface {
  protected $db;
  protected $filesystems;
  public function __construct(FactoryInterface $container) {
    $this->container = $container;
  }
  public function create($strategy, $params = []) : StrategyInterface {
    return $this->container->make($strategy, $params);
  }
}
