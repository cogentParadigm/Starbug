<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
* This file is part of StarbugPHP
* @file core/src/ControllerFactory.php
* @author Ali Gangji <ali@neonrain.com>
*/
/**
* an implementation of ControllerFactoryInterface
*/
class ControllerFactory implements ControllerFactoryInterface {
	private $inheritance;
	private $container;
	private $objects;
	public function __construct(InheritanceBuilderInterface $inheritance, ContainerInterface $container, $base_directory) {
		$this->inheritance = $inheritance;
		$this->container = $container;
		$this->base_directory = $base_directory;
		$this->objects = array();
	}
	public function has($controller) {
		return (($this->objects[$controller]) || (file_exists($this->base_directory."/var/controllers/".$controller."Controller.php")));
	}
	public function get($controller) {
		if (!isset($this->objects[$controller])) {
			$class = $this->inheritance->build("Controller", "controllers/".$controller);
			$this->objects[$controller] = $this->container->get($class);
		}
		//return the saved object
		return $this->objects[$controller];
	}
}
?>
