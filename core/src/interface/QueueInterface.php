<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/QueueInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
/**
 * a simple interface for a queue
 */
interface QueueInterface extends Countable {
	function put($item);
	function get();
	function pop($item);
	function release($item);
	function remove($item);
	function load();
	function clear();
}
