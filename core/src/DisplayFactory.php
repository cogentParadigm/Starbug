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
	private $inheritance;
	private $container;
	private $objects;
	public function __construct(InheritanceBuilderInterface $inheritance, ContainerInterface $container, $base_directory) {
		$this->inheritance = $inheritance;
		$this->container = $container;
		$this->base_directory = $base_directory;
		$this->objects = array();
	}
	public function has($display) {
		return (($this->objects[$display]) || (file_exists($this->base_directory."/var/displays/".ucwords($display)."Display.php")));
	}
	public function get($display) {
		if (!isset($this->objects[$display])) {
			$class = $this->inheritance->build("Display", "displays/".$display);
			$this->objects[$display] = $this->container->get($class);
		}
		//return the saved object
		return $this->objects[$display];
	}
}
?>
