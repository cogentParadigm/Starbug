<?php
namespace Starbug\Core;

use Starbug\Queue\QueueManagerInterface;

class ProcessQueueCommand {
  public function __construct(QueueManagerInterface $queues) {
    $this->queues = $queues;
  }
  public function run($argv) {
    $this->queues->processQueue($argv[0] ?? "default");
  }
}
