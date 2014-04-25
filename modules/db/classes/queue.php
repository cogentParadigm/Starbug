<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/db/query.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup db
 */
/**
 * The queue class. provides a generic query representation
 * usage:
 * $queue = new queue();
 * $queue->push($query);
 * $queue->push($query2);
 * $queue->execute();
 * @ingroup db
 */

class queue implements Countable, IteratorAggregate {

	/**
	 * array the queue of queries
	 */
	var $queue = array();
	
	/**
	 * Push a query or queries onto the end of the queue
	 * @param query $query the query to push onto the queue
	 */
	function push($query) {
		$queries = func_get_args();
		foreach ($queries as $query) {
			$query->validate();
			$this->queue[] = $query;
		}
	}

	/**
	 * Prepend a query or queries to the front of the queue
	 * @param query $query the query to prepend to the queue
	 */
	function unshift($query) {
		$queries = func_get_args();
		foreach ($queries as $query) {
			$query->validate();
			array_unshift($this->queue, $query);
		}
	}

	/**
	 * Execute the queue of queries
	 */
	function execute() {
		while (!empty($this->queue)) {
			$query = array_shift($this->queue);
			$query->execute();
		}
	}
	
	/**
	 * Clear the queue
	 */
	function clear() {
		$this->queue = array();
	}

	/**************************************************************
	 * interface functions
	 **************************************************************/
	
	/**
	 * Implements method from IteratorAggregate to allow iterating over this object directly
	 */
	public function getIterator() {
		return new ArrayIterator($this->queue);
	}

	/**
	 * Implements method from Countable to allow getting a count of this object
	 */
	function count() {
		return count($this->queue);
	}
}
?>
