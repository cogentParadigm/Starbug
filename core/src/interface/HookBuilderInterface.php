<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/HookBuilderInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
interface HookBuilderInterface {
	/**
	* get a controller by name
	* @param string $name the name of the controller, such as 'users'
	* @param string $type a sub type such as 'admin'
	* @return the instantiated controller
	* build("Model", "models/Users", "Table");
	* build("store/ordered")
	* build("display/label");
	*/
	function build($name);
}
