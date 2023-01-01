<?php
namespace Starbug\Imports;

use Exception;
use Starbug\Core\DatabaseInterface;
use Starbug\Imports\Read\StrategyFactoryInterface;
use Starbug\Imports\Read\StrategyInterface;
use Starbug\Imports\Transform\Factory;
use Starbug\Log\LoggerFactory;
use Starbug\State\StateInterface;

class Importer {
  public function __construct(
    StrategyFactoryInterface $readStrategyFactory,
    Factory $transformerFactory,
    OperationsRepository $operations,
    DatabaseInterface $db,
    StateInterface $state,
    LoggerFactory $loggerFactory
  ) {
    $this->readStrategyFactory = $readStrategyFactory;
    $this->transformerFactory = $transformerFactory;
    $this->operations = $operations;
    $this->db = $db;
    $this->state = $state;
    $this->logger = $loggerFactory->create("imports");
  }
  protected function getReadStrategy(Import $import): StrategyInterface {
    $strategy = $this->readStrategyFactory->create(
      $import->getReadStrategy(),
      $import->getReadStrategyParameters()
    );
    foreach ($import->getTransformers() as $transform) {
      $transformer = $this->transformerFactory->get($transform["type"]);
      if (empty($transform["model"])) {
        $transform["model"] = $import->getModel();
      }
      $strategy->addTransformer($transformer, $transform);
    }
    return $strategy;
  }
  public function run(Import $import, $options = []) {
    set_time_limit(0);
    $strategy = $this->getReadStrategy($import);
    $options = $this->getDefaultOptions($options);
    $operation = $this->operations->getOperation($import->getModel(), $import->getOperation());
    $parameters = $import->getOperationParameters();
    $index = $changed = 0;
    foreach ($strategy->getRows($import, $options) as $record) {
      $index++;
      $conn = $this->db->getConnection();
      $conn->beginTransaction();
      try {
        // Handle non-Exception errors.
        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($record, $parameters, $conn) {
          if ($conn->isTransactionActive()) {
            $conn->rollBack();
          }
          $errMessage = "Error [$errno]: $errstr - line $errline in $errfile";
          $message = json_encode(["record" => $record, "parameters" => $parameters, "message" => $errMessage]);
          $this->logger->error($message);
        }, error_reporting());

        $operation->configure($parameters);
        $operation->execute($record);

        // Handle database errors
        if ($errors = $this->db->errors(true)) {
          if ($conn->isTransactionActive()) {
            $conn->rollBack();
          }
          $errMessage = "Data Errors at " . $strategy->getLocation($record) . ":\n";
          $errMessage .= json_encode($errors, JSON_PRETTY_PRINT);
          $this->db->errors->set([]);
          $this->logger->error($errMessage);
        } else {
          $changed++;
        }
        restore_error_handler();
      } catch (Exception $e) {
        if ($conn->isTransactionActive()) {
          $conn->rollBack();
        }
        $message = "Error at ".$strategy->getLocation($record).":\n";
        $message .= json_encode(["record" => $record, "parameters" => $parameters, "message" => $e->getMessage()], JSON_PRETTY_PRINT);
        $this->logger->error($message);
        restore_error_handler();
      }
      if ($conn->isTransactionActive()) {
        $conn->commit();
      }
    }
    if ($options["now"]) {
      $this->state->set("import.sync.".$options["sync"].".lastSyncTime", $options["now"]);
    }
    return ["count" => $index, "changed" => $changed];
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
