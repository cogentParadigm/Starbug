<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/HookBuilder.php
 * @author Ali Gangji <ali@neonrain.com>
 */

class HookBuilder implements HookBuilderInterface {

	protected $locator;
	protected $hooks = array();

 function __construct(ResourceLocatorInterface $locator) {
	$this->locator = $locator;
	$this->hooks = array();
 }

	/**
	* get a controller by name
	* @param string $name the name of the controller, such as 'users'
	* @param string $type a sub type such as 'admin'
	* @return the instantiated controller
	* build("Model", "models/Users", "Table");
	* build("store/ordered")
	* build("display/label");
	*/
 function build($name) {
	if (!$root) $root = $name;
	$key = $name."/".$path;

	if (!isset($this->hooks[$name])) {
	$hooks = array();
	$parts = explode("/", $name);
	$class = "hook_".$parts[0]."_".$parts[1];

	//get extending classes
	$files = $this->locate($name.".php", "hooks");
	$count = count($files);
	$search = "class $class ";

	//loop through found classes
	for ($i = 0; $i < $count; $i++) {
	 //get file contents
	 $contents = file_get_contents($files[$i]);
	 $replace = "class ".$class.$i." ";
	 //replace and eval
	 eval('?>'.str_replace($search, $replace, $contents));
	 $hooks[] = $class.$i;
	}

	$this->hooks[$name] = $hooks;
	}

	return $this->hooks[$name];
 }
}
