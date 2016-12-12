<?php
# Copyright (C) 2008-2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file modules/db/src/QueryBuilderFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
namespace Starbug\Core;
use \Interop\Container\ContainerInterface;
/**
* an implementation of ModelFactoryInterface
*/
class CollectionFactory implements CollectionFactoryInterface {
	protected $locator;
	protected $container;
	public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container) {
		$this->locator = $locator;
		$this->container = $container;
	}
	public function get($collection) {
		$collection = ucwords($collection)."Collection";
		$locations = $this->locator->locate($collection.".php", "collections");
		end($locations);
		$namespace = key($locations);
		if (empty($namespace)) {
			$namespace = "Starbug\Core";
			$collection = "Collection";
		}
		$object = $this->container->get($namespace."\\".$collection);
		return $object;
	}
}
?>
