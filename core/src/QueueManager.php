<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/QueueInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
/**
 * a simple interface for a queue
 */
class QueueManager implements QueueManagerInterface {
	protected $queues = array();
	protected $db;
	protected $tasks;
	function __construct(DatabaseInterface $db, TaskFactoryInterface $tasks) {
		$this->db = $db;
		$this->tasks = $tasks;
	}
	function queue($name) {
		if (!isset($this->queues[$name])) {
			$this->queues[$name] = new Queue($this->db, $name);
		}
		return $this->queues[$name];
	}
	function put($queue, $data = array(), $status = "pending") {
		$job = array("data" => $data, "status" => $status);
		$this->queue($queue)->put($job);
	}
	function process($queue, $duration = 600) {
		$queue = $this->queue($queue);
		$end = time() + $duration;
		while (time() < $end && ($item = $queue->get())) {
			$this->tasks->get($item['queue'])->process($item, $queue);
		}
	}
}
