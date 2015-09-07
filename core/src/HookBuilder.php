<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/HookBuilder.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
class HookBuilder implements HookBuilderInterface {

	protected $locator;
	protected $hooks = array();

	function __construct(ResourceLocatorInterface $locator) {
		$this->locator = $locator;
		$this->hooks = array();
	}

	/**
	* get a controller by name
	* @param string $name the name of the hook, such as 'store/ordered'
	* @return an array of class names
	* build("store/ordered")
	* build("display/label");
	*/
	function build($name) {
		if (!isset($this->hooks[$name])) {
			$hooks = array();
			$parts = explode("/", $name);
			$class = "hook_".$parts[0]."_".$parts[1];

			//get extending classes
			$namespaces = $this->locator->locate_namespaces($name.".php", "hooks");

			//loop through found classes
			foreach ($namespaces as $namespace) {
				$hooks[] = $namespace.$class;
			}

			$this->hooks[$name] = $hooks;
		}

		return $this->hooks[$name];
	}
}
