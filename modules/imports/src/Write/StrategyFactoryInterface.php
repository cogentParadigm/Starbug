<?php
namespace Starbug\Imports\Write;

interface StrategyFactoryInterface {
  public function create($strategy, $params = []) : StrategyInterface;
}
