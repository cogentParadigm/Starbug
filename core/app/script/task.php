<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file script/migrate.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup script
 */
class TaskCommand {
	function __construct(QueueManagerInterface $queues) {
		$this->queues = $queues;
	}
	public function run($argv) {
		$this->queues->process($argv[0]);
	}
}
?>
