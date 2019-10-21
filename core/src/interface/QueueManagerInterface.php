<?php
namespace Starbug\Core;

/**
 * A simple interface for a queue.
 */
interface QueueManagerInterface {
  /**
   * Put a job into a queue
   *
   * @param string $queue the name of the queue
   * @param array $job the job data
   */
  public function put($queue, $data = [], $status = "pending");
  /**
   * Process jobs in a queue
   *
   * @param string $queue the name of the queue
   * @param int $duration how long to process items
   */
  public function process($queue, $duration = 600);
}
