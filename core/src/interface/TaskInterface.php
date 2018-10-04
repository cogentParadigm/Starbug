<?php
namespace Starbug\Core;
/**
 * a simple interface for a queue
 */
interface TaskInterface {
	function process($item, QueueInterface $queue);
}
