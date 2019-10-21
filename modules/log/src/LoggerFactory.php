<?php
namespace Starbug\Log;

use Monolog\Logger;
use Starbug\Core\DatabaseInterface;

class LoggerFactory {
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function create($channel) {
    $logger = new Logger($channel);
    $logger->pushHandler(new DatabaseLogHandler($this->db, "error_log"));
    return $logger;
  }
}
