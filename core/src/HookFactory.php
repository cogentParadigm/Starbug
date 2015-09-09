<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/DisplayFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;
/**
* an implementation of DisplayFactoryInterface
*/
class HookFactory implements HookFactoryInterface {
	private $hooks;
	private $container;
	private $classes = array();
	public function __construct(ContainerInterface $container, HookBuilderInterface $hooks) {
		$this->hooks = $hooks;
		$this->container = $container;
	}
	public function get($hook) {
		$classes = $this->hooks->build($hook);
		$hooks = array();
		foreach ($classes as $class) {
			$hooks[] = $this->container->build($class);
		}
		return $hooks;
	}
}
