<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/ControllerFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;
/**
* an implementation of ControllerFactoryInterface
*/
class ControllerFactory implements ControllerFactoryInterface {
	private $locator;
	private $container;
	public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container) {
		$this->locator = $locator;
		$this->container = $container;
	}
	public function get($controller) {
		$controller = ucwords($controller)."Controller";
		$namespace = end($this->locator->locate_namespaces($controller.".php", "controllers"));
		if (empty($namespace)) {
			$namespace = "Starbug\Core\\";
			$controller = "Controller";
		}
		$object = $this->container->get($namespace.$controller);
		return $object;
	}
}
