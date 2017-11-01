<?php
namespace Starbug\Core;
use \SplQueue;
/**
 * usage:
 * $queue = new QueryQueue();
 * $queue->push($query);
 * $queue->push($query2);
 * $queue->execute();
 * @ingroup db
 */
class QueryQueue extends SplQueue {
	public $active;
	/**
	 * Push a query or queries onto the end of the queue
	 * @param query $query the query to push onto the queue
	 */
	function push($query) {
		$query->validate();
		parent::push($query);
	}
	/**
	 * Prepend a query or queries to the front of the queue
	 * @param query $query the query to prepend to the queue
	 */
	function unshift($query) {
		$query->validate();
		parent::unshift($query);
	}
	/**
	 * Execute the queue of queries
	 */
	function execute() {
		while (!$this->isEmpty()) {
			$this->active = $this->shift();
			$this->active->execute();
		}
	}
}
