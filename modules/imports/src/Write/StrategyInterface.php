<?php
namespace Starbug\Imports\Write;

use Exception;
use Starbug\Imports\Read\StrategyInterface as ReadStrategyInterface;

interface StrategyInterface {
  public function run(ReadStrategyInterface $readStrategy, $options = []);
  public function handleError($level, $message, $file = null, $line = null);
  public function handleException(Exception $exception);
}
