<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/ContainerInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */

class Container implements ContainerInterface {

	private $map;

 function __construct() {
	 $this->map = array();
	 $this->register('ContainerInterface', $this, true);
 }

	/**
	 * get an object from the container
	 * @param string $name the name of the object
	 * @return mixed object from container
	 */
 function get($name) {
	if (!isset($this->map[$name])) {
	  $this->map[$name] = array("class" => $name);
	}
	if (isset($this->map[$name]['value'])) {
	  return $this->map[$name]['value'];
	} else {
	  $value = $this->map[$name]['class'];
	 if (interface_exists($value)) {
	   $value = str_replace("Interface", "", $value);
	 }
	 if (class_exists($value)) {
	   $this->map[$name]['value'] = $this->build($value);
	   return $this->get($name);
	 } else {
	   throw new Exception("Dependency not found: ".$name);
	 }
	}
 }

	/**
	 *
	 */
 function update($name) {
	 $this->destroy($name);
	 return $this->get($name);
 }

	/**
	 *
	 */
 function destroy($name) {
	 unset($this->map[$name]['value']);
 }

	/**
	* determine if an object is available to the container
	* @param string $name the name of the object
	* @return bool true if the object can be provided
	*/
 function has($name) {
	 return (isset($this->map[$name]) || class_exists($name));
 }

	/**
	* register an item in the container
	* @param string $name the name of the object
	* @param mixed $value the object
	* @param bool $literal set true to provide the value directly without any object construction
	*/
 function register($name, $value, $literal = false) {
	 $this->map[$name] = $literal ? array("value" => $value) : array("class" => $value);
 }

 function build($name, $options = array()) {
	$class = new ReflectionClass($name);
	$args = array();
	$constructor = $class->getConstructor();
	if ($constructor) {
	$params = $constructor->getParameters();
	foreach ($params as $param) {
	   $type = $param->getClass();
	if (isset($options[$param->getName()])) {
		$args[] = $options[$param->getName()];
	} else if ($type) {
		$args[] = $this->get($type->name);
	} else if (!$this->has($param->getName()) && $param->isDefaultValueAvailable()) {
		$args[] = $param->getDefaultValue();
	} else {
		$args[] = $this->get($param->getName());
	}
	}
	}
	return $class->newInstanceArgs($args);
 }
}
