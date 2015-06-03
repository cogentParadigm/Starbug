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
interface TaskInterface {
	function process($item, QueueInterface $queue);
}
