<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/QueueManagerInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
/**
 * a simple interface for a queue
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
