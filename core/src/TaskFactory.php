<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/TaskFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;
/**
* an implementation of TaskFactoryInterface
*/
class TaskFactory implements TaskFactoryInterface {
	protected $container;
	protected $locator;
	protected $tasks = array();
	public function __construct(ContainerInterface $container, ResourceLocatorInterface $locator) {
		$this->container = $container;
		$this->locator = $locator;
	}
	public function get($task) {
		if (!isset($this->tasks[$task])) {
			$class = ucwords($task)."Task";
			$namespace = end($this->locator->get_namespaces($class, "tasks"));
			$this->tasks[$task] = $this->container->build($namespace.$class);
		}
		return $this->tasks[$task];
	}
}
