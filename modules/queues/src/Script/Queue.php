<?php
namespace Starbug\Queues\Script;

use Starbug\Queue\QueueManagerInterface;

class Queue {
  public function __construct(
    protected QueueManagerInterface $queues
  ) {
  }
  public function __invoke($positional, $named) {
    $worker = array_shift($positional);
    $this->queues->put($worker, $named);
  }
}
