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
	public function get($displays) {
		if (!is_array($displays)) $displays = [$displays];
		foreach ($displays as $display) {
			if ($display = $this->locator->className($display)) {
				$object = $this->container->get($display);
				if ($object instanceof Display) {
					return $object;
				} else {
					throw new Exception("DisplayFactoryInterface contract violation. ".$display." is not an instance of Starbug\\Core\\Display.");
				}
			}
		}
		throw new Exception("Display not found: ".implode(", ", $displays));
	}
}
