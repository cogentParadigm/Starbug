<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/InheritanceBuilderInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */

interface InheritanceBuilderInterface {
	/**
	* build an inheritance chain
	* @param string $name the name of the controller, such as 'users'
	* @param string $type a sub type such as 'admin'
	* @return the instantiated controller
	* build("Model", "models/Users", "Table");
	* build("Controller", "controllers/RegisterController")
	* build("Display", "displays/GridDisplay");
	*/
	function build($name, $path, $root=false);
}
