<?php
namespace Starbug\Imports\Read;

interface StrategyFactoryInterface {
  public function create($strategy, $params = []) : StrategyInterface;
}
