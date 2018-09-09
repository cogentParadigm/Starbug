<?php
namespace Starbug\Core;

/**
 * A simple interface for a task in a queue.
 */
interface TaskInterface {
  function process($item, QueueInterface $queue);
}
