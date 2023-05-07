<?php
namespace Starbug\Queues\Script;

use Starbug\Queue\QueueManagerInterface;

class ProcessQueue {
  public function __construct(QueueManagerInterface $queues) {
    $this->queues = $queues;
  }
  public function __invoke($argv) {
    $this->queues->processQueue($argv[0] ?? "default");
  }
}
