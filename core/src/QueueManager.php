<?php
namespace Starbug\Core;

/**
 * A simple interface for a queue.
 */
class QueueManager implements QueueManagerInterface {
  protected $queues = [];
  protected $db;
  protected $tasks;
  public function __construct(DatabaseInterface $db, TaskFactoryInterface $tasks) {
    $this->db = $db;
    $this->tasks = $tasks;
  }
  public function queue($name) {
    if (!isset($this->queues[$name])) {
      $this->queues[$name] = new Queue($this->db, $name);
    }
    return $this->queues[$name];
  }
  public function put($queue, $data = [], $status = "pending") {
    $job = ["data" => $data, "status" => $status];
    $this->queue($queue)->put($job);
  }
  public function process($queue, $duration = 600) {
    $queue = $this->queue($queue);
    $end = time() + $duration;
    while (time() < $end && ($item = $queue->get())) {
      $this->tasks->get($item['queue'])->process($item, $queue);
    }
  }
}
