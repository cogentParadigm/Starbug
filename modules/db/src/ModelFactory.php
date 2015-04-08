<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file modules/db/src/QueryBuilderFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
/**
* an implementation of ModelFactoryInterface
*/
class ModelFactory implements ModelFactoryInterface {
	private $inheritance;
	private $container;
	private $objects;
	public function __construct(InheritanceBuilderInterface $inheritance, ContainerInterface $container, $base_directory) {
		$this->inheritance = $inheritance;
		$this->container = $container;
		$this->base_directory = $base_directory;
		$this->objects = array();
	}
	public function has($collection) {
		return (($this->objects[$collection]) || (file_exists($this->base_directory."/var/models/".ucwords($collection)."Model.php")));
	}
	public function get($collection) {
		if (!isset($this->objects[$collection])) {
			$class = $this->inheritance->build("Model", "models/".ucwords($collection), "Table");
			$this->objects[$collection] = $this->container->get($class);
		}
		//return the saved object
		return $this->objects[$collection];
	}
}
?>