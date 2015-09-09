<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/QueueInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
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
