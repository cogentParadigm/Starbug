<?php

namespace Starbug\Log;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Starbug\Core\DatabaseInterface;

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
  protected $defaultFields = ['channel', 'level', 'message', 'time'];


  /**
   * Constructor of this class, sets the PDO and calls parent constructor
   *
   * @param DatabaseInterface $db Database connection.
   * @param string $table The name of the table.
   * @param array $additionalFields Additional fields to store in the database.
   * @param integer|string $level The minimum logging level at which this handler will be triggered.
   * @param boolean $bubble Whether the messages that are handled can bubble up the stack or not.
   */
  public function __construct(DatabaseInterface $db, $table, $additionalFields = [], $level = Logger::DEBUG, $bubble = true) {
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
  protected function write(array $record) {
    if (isset($record['extra'])) {
      $record['context'] = array_merge($record['context'], $record['extra']);
    }

    $write = array_merge([
      'channel' => $record['channel'],
      'level' => $record['level'],
      'message' => $record['message'],
      'time' => $record['datetime']->format('Y-m-d H:i:s')
    ], $record['context']);

    foreach ($write as $key => $value) {
      if (!in_array($key, $this->fields)) {
        unset($write[$key]);
      }
    }

    $this->db->store($this->table, $write);
  }
}
