<?php
namespace Starbug\Imports;

use Exception;
use Starbug\Imports\Read\StrategyFactoryInterface;
use Starbug\Imports\Read\StrategyInterface;
use Starbug\Imports\Transform\Factory;
use Starbug\Imports\Write\StrategyFactoryInterface as WriteStrategyFactoryInterface;
use Starbug\Imports\Write\StrategyInterface as WriteStrategyInterface;
use Starbug\Log\LoggerFactory;
use Starbug\State\StateInterface;

class Importer {
  protected $logger;
  public function __construct(
    protected StrategyFactoryInterface $readStrategyFactory,
    protected WriteStrategyFactoryInterface $writeStrategyFactory,
    protected Factory $transformerFactory,
    protected StateInterface $state,
    LoggerFactory $loggerFactory
  ) {
    $this->logger = $loggerFactory->create("imports");
  }
  protected function getReadStrategy(Import $import): StrategyInterface {
    $strategy = $this->readStrategyFactory->create(
      $import->getReadStrategy(),
      $import->getReadStrategyParameters()
    );
    $strategy->setFields($import->getFields());
    foreach ($import->getTransformers() as $transform) {
      $transformer = $this->transformerFactory->get($transform["type"]);
      $strategy->addTransformer($transformer, $transform + ["model" => $import->getModel()]);
    }
    return $strategy;
  }
  protected function getWriteStrategy(Import $import): WriteStrategyInterface {
    $strategy = $this->writeStrategyFactory->create(
      $import->getWriteStrategy(),
      $import->getWriteStrategyParameters()
    );
    return $strategy;
  }
  public function run(Import $import, $options = []) {
    set_time_limit(0);
    $readStrategy = $this->getReadStrategy($import);
    $writeStrategy = $this->getWriteStrategy($import);
    $options = $this->getDefaultOptions($options);

    try {
      // Handle non-Exception errors.
      set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($writeStrategy) {
        if ($message = $writeStrategy->handleError($errno, $errstr, $errfile, $errline)) {
          $this->logger->error($message);
        }
      }, error_reporting());

      $writeStrategy->run($readStrategy, $options);

      restore_error_handler();
    } catch (Exception $e) {
      if ($message = $writeStrategy->handleException($e)) {
        $this->logger->error($message, ["trace" => $e->getTrace()]);
      }
      restore_error_handler();
    }
    if ($options["now"]) {
      $this->state->set("import.sync.".$options["sync"].".lastSyncTime", $options["now"]);
    }
  }
  protected function getDefaultOptions($options = []) {
    $now = false;
    if (!empty($options["sync"])) {
      $now = date("Y-m-d H:i:s");
      if (empty($options["reset"])) {
        $lastSyncTime = $this->state->get("import.sync.".$options["sync"].".lastSyncTime", "0000-00-00 00:00:00");
        $options["lastSyncTime"] = $lastSyncTime;
      }
    }
    $options["now"] = $now;
    return $options;
  }
}
