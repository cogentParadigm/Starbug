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
	public function __construct(InheritanceBuilderInterface $inheritance, ContainerInterface $container) {
		$this->inheritance = $inheritance;
		$this->container = $container;
	}
	public function get($display, $model=null, $name=null, $options=array()) {
		$class = $this->inheritance->build("Display", "displays/".ucwords($display)."Display");
		return $this->container->build($class, array('model' => $model, 'name' => $name, 'options' => $options));
	}
}
?>
