<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/TaskFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
/**
* an implementation of TaskFactoryInterface
*/
class TaskFactory implements TaskFactoryInterface {
	protected $container;
	protected $tasks = array();
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}
	public function get($task) {
		if (!isset($this->tasks[$task])) {
			$this->tasks[$task] = $this->container->build(ucwords($task)."Task");
		}
		return $this->tasks[$task];
	}
}
