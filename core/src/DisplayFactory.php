<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/DisplayFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
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
		return $this->container->build($display);
	}
}
