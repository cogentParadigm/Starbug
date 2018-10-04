<?php
namespace Starbug\Core;

use \Countable;
/**
 * a simple interface for a queue
 */
interface QueueInterface extends Countable {
	function put($item);
	function get();
	function release($item);
	function remove($item);
	function success($item, $status = "success");
	function failure($item, $message = "", $status = "failed");
	function load();
	function clear();
}
