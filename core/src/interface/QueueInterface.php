<?php
namespace Starbug\Core;

use \Countable;

/**
 * A simple interface for a queue.
 */
interface QueueInterface extends Countable {
  public function put($item);
  public function get();
  public function release($item);
  public function remove($item);
  public function success($item, $status = "success");
  public function failure($item, $message = "", $status = "failed");
  public function load();
  public function clear();
}
