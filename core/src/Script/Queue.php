<?php
namespace Starbug\Core\Script;

use Starbug\Queue\QueueManagerInterface;

class Queue {
  public function __construct(QueueManagerInterface $queues) {
    $this->queues = $queues;
  }
  public function __invoke($positional, $named) {
    $worker = array_shift($positional);
    $this->queues->put($worker, $named);
  }
}
