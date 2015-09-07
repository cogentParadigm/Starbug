<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/InheritanceBuilder.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
class InheritanceBuilder implements InheritanceBuilderInterface {

	protected $locator;
	protected $classes = array();
	protected $base_directory;

	function __construct(ResourceLocatorInterface $locator, $base_directory) {
		$this->locator = $locator;
		$this->base_directory = $base_directory;
		$this->classes = array();
	}

	/**
	* build an inheritance chain
	* @param string $name the name of the controller, such as 'users'
	* @param string $type a sub type such as 'admin'
	* @return the instantiated controller
	* build("Model", "models/Users", "Table");
	* build("Controller", "controllers/RegisterController")
	* build("Display", "displays/GridDisplay");
	*/
	function build($name, $path, $root = false) {
		if (!$root) $root = $name;
		$key = $name."/".$path;

		if (!isset($this->classes[$key])) {
			$parts = explode("/", $path);
			$class = $parts[1];
			$last = $root;

			$generated = $this->base_directory."/var/".$parts[0]."/".$class.$name.".php";
			if (file_exists($generated)) {
				include($generated);
				$last = $class.$name;
			}

			//get extending classes
			$files = $this->locator->locate("$class.php", $parts[0]);
			$count = count($files);
			$search = "class $class {";

			//loop through found classes
			for ($i = 0; $i < $count; $i++) {
				//get file contents
				$contents = file_get_contents($files[$i]);
				//make class name unique and extend the previous class
				$class = str_replace(array($this->base_directory.'/', '/'), array('', '_'), reset(explode('/'.$parts[0].'/', $files[$i])))."__$class";
				$replace = "class $class extends $last {";
				//replace and eval
				eval('?>'.str_replace($search, $replace, $contents));
				//set $last for the next round
				$last = $class;
			}

			//return the base class if no others
			if ($count == 0) $class = $last;

			$this->classes[$key] = $class;
		}

		return $this->classes[$key];
	}
}
