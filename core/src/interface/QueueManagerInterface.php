<?php
namespace Starbug\Core;

/**
 * A simple interface for a queue.
 */
interface QueueManagerInterface {
  /**
   * put a job into a queue
   * @param string $queue the name of the queue
   * @param array $job the job data
   */
  function put($queue, $data = array(), $status = "pending");
  /**
   * process jobs in a queue
   * @param string $queue the name of the queue
   * @param int $duration how long to process items
   */
  function process($queue, $duration = 600);
}
