<?php
namespace Starbug\Imports\Write;

use Starbug\Db\DatabaseInterface;
use Exception;
use Starbug\Imports\Read\StrategyInterface as ReadStrategyInterface;
use Starbug\Operation\OperationInterface;

/**
 * This strategy writes to the database using a configurable operation.
 * Each row is written one at a time, inside a transaction.
 */
class DatabaseStrategy implements StrategyInterface {
  protected $readStrategy;
  protected $options = [];
  protected $record;

  public function __construct(
    protected DatabaseInterface $db,
    protected OperationInterface $operation
  ) {
    $this->db = $db;
    $this->operation = $operation;
  }
  public function run(ReadStrategyInterface $readStrategy, $options = []) {
    $this->readStrategy = $readStrategy;
    $this->options = $options;

    foreach ($readStrategy->getRows($options) as $record) {
      $this->record = $record;

      // @phpstan-ignore-next-line
      $conn = $this->db->getConnection();
      $conn->beginTransaction();

      $this->operation->execute($record);

      if ($errors = $this->db->errors(true)) {
        trigger_error(json_encode($errors, JSON_PRETTY_PRINT));
        // @phpstan-ignore-next-line
        $this->db->errors->set([]);
      }

      $this->commit();
    }
  }
  public function handleError($level, $message, $file = null, $line = null) {
    $this->rollBack();
    if ($level == E_USER_NOTICE) {
      $errMessage = "Error at ".$this->readStrategy->getLocation($this->record).":\n".$message;
    } else {
      $errMessage = "Error [$level]: $message - line $line in $file";
    }
    return json_encode([
      "record" => $this->record,
      "options" => $this->options,
      "message" => $errMessage
    ]);
  }
  public function handleException(Exception $exception) {
    $this->rollBack();
    $message = "Error at ".$this->readStrategy->getLocation($this->record).":\n";
    $message .= json_encode([
      "record" => $this->record,
      "options" => $this->options,
      "message" => $exception->getMessage()
    ], JSON_PRETTY_PRINT);
    return $message;
  }
  protected function rollBack() {
    // @phpstan-ignore-next-line
    $conn = $this->db->getConnection();
    if ($conn->isTransactionActive()) {
      $conn->rollBack();
    }
  }
  protected function commit() {
    // @phpstan-ignore-next-line
    $conn = $this->db->getConnection();
    if ($conn->isTransactionActive()) {
      $conn->commit();
    }
  }
}
