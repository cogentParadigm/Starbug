<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/DisplayFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;
use \Interop\Container\ContainerInterface;
/**
* an implementation of DisplayFactoryInterface
*/
class DisplayFactory implements DisplayFactoryInterface {
	protected $locator;
	protected $container;
	public function __construct(ContainerInterface $container, ResourceLocatorInterface $locator) {
		$this->container = $container;
		$this->locator = $locator;
	}
	public function get($display) {
		$locations = $this->locator->locate($display.".php", "displays");
		end($locations);
		$namespace = key($locations);
		if (empty($namespace)) $namespace = "Starbug\Core";
		return $this->container->make($namespace."\\".$display);
	}
}
