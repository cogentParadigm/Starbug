<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/HelperFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;
/**
* an implementation of HelperFactoryInterface
*/
class HelperFactory implements HelperFactoryInterface {
	protected $locator;
	protected $container;
	public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container) {
		$this->container = $container;
		$this->locator = $locator;
	}
	public function get($helper) {
		$namespace = end($this->locator->locate_namespaces($helper.".php", "helpers"));
		return $this->container->build($namespace.$helper);
	}
}
