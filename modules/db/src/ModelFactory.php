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
	private $locator;
	private $container;
	private $objects;
	public function __construct(ResourceLocatorInterface $locator, ContainerInterface $container) {
		$this->locator = $locator;
		$this->container = $container;
		$this->objects = array();
	}
	public function has($collection) {
		return (($this->objects[$collection]) || (file_exists(BASE_DIR."/var/models/".ucwords($collection)."Model.php")));
	}
	public function build($collection) {
		$class = $model = ucwords($collection);
		if (!isset($this->objects[$collection])) {
			if (file_exists(BASE_DIR."/var/models/".$class."Model.php")) {
				//include the base model
				include(BASE_DIR."/var/models/".$class."Model.php");
				$last = $class."Model";

				//get additional models
				$models = $this->locator->locate("$class.php", "models");
				$count = count($models);
				$search = "class $class {";

				//loop through found models
				for ($i = 0; $i < $count; $i++) {
					//get file contents
					$contents = file_get_contents($models[$i]);
					//make class name unique and extend the previous class
					$class = str_replace(array(BASE_DIR.'/', '/'), array('', '_'), reset(explode('/models/', $models[$i])))."__".$model;
					$replace = "class $class extends $last {";
					//replace and eval
					eval('?>'.str_replace($search, $replace, $contents));
					//set $last for the next round
					$last = $class;
				}

				//return the base model if no others
				if ($count == 0) $class .= "Model";

			} else $class = "Table"; //return the base table if the model does not exist

			//instantiate save the object
			$this->objects[$collection] = $this->container->get($class);
		}

		//return the saved object
		return $this->objects[$collection];
	}
}
?>
