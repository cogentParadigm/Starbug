<?php
namespace Starbug\Log;

use Monolog\Logger;

class LoggerFactory {
  protected $handlers;
  public function __construct($handlers = []) {
    $this->handlers = $handlers;
  }
  public function create($channel) {
    return new Logger($channel, $this->handlers);
  }
}
