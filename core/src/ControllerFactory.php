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
	public function __construct(InheritanceBuilderInterface $inheritance, ContainerInterface $container) {
		$this->inheritance = $inheritance;
		$this->container = $container;
	}
	public function get($controller) {
		$controller = ucwords($controller)."Controller";
		$class = $this->inheritance->build("Controller", "controllers/".$controller);
		$object = $this->container->get($class);
		echo $class;
		return $object;
	}
}
