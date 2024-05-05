<?php
namespace Starbug\Queues\Script;

use Starbug\Queue\QueueManagerInterface;

class ProcessQueue {
  public function __construct(
    protected QueueManagerInterface $queues
  ) {
  }
  public function __invoke($argv) {
    $this->queues->processQueue($argv[0] ?? "default");
  }
}
