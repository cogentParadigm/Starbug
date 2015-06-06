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
	protected $inheritance;
	protected $container;
	protected $objects;
	protected $validation;
	public function __construct(InheritanceBuilderInterface $inheritance, ContainerInterface $container, ValidationInterface $validation, $base_directory) {
		$this->inheritance = $inheritance;
		$this->container = $container;
		$this->validation = $validation;
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
			$this->objects[$collection]->set_validation($this->validation);
		}
		//return the saved object
		return $this->objects[$collection];
	}
}
?>
