<?php
namespace Starbug\Db;

use \SplQueue;

/**
 * Usage:
 * $queue = new QueryQueue();
 * $queue->push($query);
 * $queue->push($query2);
 * $queue->execute();
 */
class QueryQueue extends SplQueue {
  public $active;
  /**
   * Push a query or queries onto the end of the queue
   *
   * @param query $query the query to push onto the queue
   */
  public function push($query) {
    $query->validate();
    parent::push($query);
  }
  /**
   * Prepend a query or queries to the front of the queue
   *
   * @param query $query the query to prepend to the queue
   */
  public function unshift($query) {
    $query->validate();
    parent::unshift($query);
  }
  /**
   * Execute the queue of queries
   */
  public function execute() {
    while (!$this->isEmpty()) {
      $this->active = $this->shift();
      $this->active->execute();
    }
  }
}
