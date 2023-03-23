<?php

namespace Starbug\Log;

use DateTime;
use Doctrine\DBAL\Connection;
use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Monolog handler which writes to error_log table.
 */
class DatabaseLogHandler extends AbstractProcessingHandler {

  /**
   * The table to store the logs in.
   *
   * @var string
   */
  protected $table;

  /**
   * Default fields that are stored in db.
   *
   * @var array
   */
  protected $defaultFields = ["channel", "level", "message", "time", "context"];


  /**
   * Constructor of this class, sets the PDO and calls parent constructor
   *
   * @param Connection $db Database connection.
   * @param string $table The name of the table.
   * @param array $additionalFields Additional fields to store in the database.
   * @param integer|string $level The minimum logging level at which this handler will be triggered.
   * @param boolean $bubble Whether the messages that are handled can bubble up the stack or not.
   */
  public function __construct(Connection $db, $table, $additionalFields = [], $level = Logger::DEBUG, $bubble = true) {
    $this->db = $db;
    $this->table = $table;
    $this->fields = array_merge($this->defaultFields, $additionalFields);
    parent::__construct($level, $bubble);
  }

  /**
   * Writes the record down to the log of the implementing handler
   *
   * @param array $record The record data.
   *
   * @return void
   */
  protected function write(array $record): void {
    if (isset($record['extra'])) {
      $record['context'] = array_merge($record['context'], $record['extra']);
    }
    if (isset($record["datetime"])) {
      $record["time"] = $record["datetime"];
    }

    $placeholders = [];
    $values = [];
    foreach ($this->fields as $key) {
      $value = "NULL";
      if (isset($record[$key])) {
        $value = $record[$key];
      } elseif (isset($record["context"][$key])) {
        $value = $record["context"][$key];
      }
      if ($value instanceof DateTime) {
        $value = $value->format("Y-m-d H:i:s");
      }
      if (is_array($value)) {
        $value = json_encode($value, JSON_PRETTY_PRINT);
      }
      $placeholders[] = "?";
      $values[] = $value;
    }
    $keys = implode(", ", $this->fields);
    $placeholders = implode(", ", $placeholders);
    $statement = $this->db->prepare("INSERT INTO {$this->table} ({$keys}) VALUES ({$placeholders})");
    $statement->execute($values);
  }
}
