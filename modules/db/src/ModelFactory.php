<?php
# Copyright (C) 2008-2010 Ali Gangji
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
class ModelFactory implements ModelFactoryInterface {
	protected $locator;
	protected $container;
	protected $objects;
	public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container, $base_directory) {
		$this->locator = $locator;
		$this->container = $container;
		$this->base_directory = $base_directory;
		$this->objects = array();
	}
	public function has($collection) {
		return (!empty($collection) && (($this->objects[$collection]) || (file_exists($this->base_directory."/var/models/".ucwords($collection)."Model.php"))));
	}
	public function get($collection) {
		if (!isset($this->objects[$collection])) {
			$class = ucwords($collection);
			$namespace = end($this->locator->locate_namespaces($class.".php", "models"));
			if (empty($namespace)) {
				$namespace = "Starbug\Core\\";
				if ($this->has($collection)) {
					$class .= "Model";
				} else {
					$class = "Table";
				}
			}
			$this->objects[$collection] = $this->container->get($namespace.$class);
		}
		//return the saved object
		return $this->objects[$collection];
	}
}
?>
