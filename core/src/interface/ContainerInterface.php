<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/ContainerInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */

interface ContainerInterface {

	/**
	 * get an object from the container
	 * @param string $name the name of the object
	 * @return mixed object from container
	 */
	function get($name);

	/**
	 * determine if an object is available to the container
	 * @param string $name the name of the object
	 * @return bool true if the object can be provided
	 */
	function has($name);

	/**
	 * update an object in the container
	 * @param string $name the name of the object
	 * @return mixed object from container
	 */
	function update($name);

	/**
	 * destroy an object in the container
	 * @param string $name the name of the object
	 */
	function destroy($name);

	/**
	 * register an item in the container
	 * @param string $name the name of the object
	 * @param mixed $value the object
	 * @param bool $literal set true to provide the value directly without any object construction
	 */
	function register($name, $value, $literal=false);
	/**
	 * build an object
	 * @param string $name the name of the object
	 * @param array $options dependencies to override
	 */
	function build($name, $options=array());
}
