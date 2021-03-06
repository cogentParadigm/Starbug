<?php
namespace Starbug\Core;

class TaskCommand {
  public function __construct(QueueManagerInterface $queues) {
    $this->queues = $queues;
  }
  public function run($argv) {
    $this->queues->process($argv[0]);
  }
}
