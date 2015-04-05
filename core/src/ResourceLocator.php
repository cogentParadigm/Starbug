<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/ResourceLocator.php
 * @author Ali Gangji <ali@neonrain.com>
 */

class ResourceLocator implements ResourceLocatorInterface {

	private $base_directory;
	private $modules;

 function __construct($base_directory = "", $modules = array()) {
	 $this->base_directory = $base_directory;
	 $this->modules = $modules;
 }

	public function get($mid) {
		return $mid;
	}

	public function set($mid, $path) {
		$this->modules[$mid] = $path;
	}

	/**
	 * get module path chain
	 * @ingroup modules
	 * @param string $name the filename
	 * @param string $dir the directory within app/ core/app/ or module dir to look in. default is templates/
	 * @TODO allow boolean return
	 */
	function locate($name, $scope = "templates") {
		if (!empty($scope)) $scope .= "/";
		$path = $scope.$name;
		$paths = array();
	 foreach ($this->modules as $mid => $module_path) {
		 $target = $this->base_directory."/".$module_path."/".$path;
		 if (file_exists($target)) $paths[] = $target;
	 }
		return $paths;
	}

	/**
	* get a controller by name
	* @param string $name the name of the controller, such as 'users'
	* @param string $type a sub type such as 'admin'
	* @return the instantiated controller
	* build_class("displays/GridDisplay", "lib/Display", "core");
	*/
	function get_module_class($path, $base = "lib/Controller", $mid = "core") {
		static $classes;
		efault($classes, array());

		$class_key = implode("/", array($mid, $base, $path));

	 if (!isset($classes[$class_key])) {
		 $parts = explode("/", $path);
		 $class = $parts[1];

		 $last = end(explode("/", $base));

		 //get extending classes
		 $files = $this->locate("$class.php", $parts[0]);
		 $count = count($files);
		 $search = "class $class {";

		 //loop through found classes
	  for ($i = 0; $i < $count; $i++) {
		  //get file contents
		  $contents = file_get_contents($files[$i]);
		  //make class name unique and extend the previous class
		  $class = str_replace(array(BASE_DIR.'/', '/'), array('', '_'), reset(explode('/'.$parts[0].'/', $files[$i])))."__$class";
		  $replace = "class $class extends $last {";
		  //replace and eval
		  eval('?>'.str_replace($search, $replace, $contents));
		  //set $last for the next round
		  $last = $class;
	  }

		 //return the base class if no others
		 if ($count == 0) $class = $last;

		 $classes[$class_key] = $class;
	 }

		//instantiate save the object
		return $classes[$class_key];
	}

	/**
	* build a hook class
	* @param string $path the sub-path to the hook such as 'store/ordered'
	* @param string $base the module relative location of the base class such as 'classes/QueryHook'
	* @param string $mid the module that contains the base class, such as 'db'
	* @return the instantiated hook
	*/
	function build_hook($path, $base, $mid = "core") {
		static $hooks;
		efault($hooks, array());

		$hook_key = implode("/", array($mid, $base, $path));

	 if (!isset($hooks[$hook_key])) {
		 $class = "hook_".str_replace("/", "_", $path);

		 $parts = explode("/", $base);
		 $last = end($parts);

		 //get extending hooks
		 $files = $this->locate($path.".php", "hooks");
		 $count = count($files);
		 $search = "class $class {";

		 //loop through found hooks
	  for ($i = 0; $i < $count; $i++) {
		  //get file contents
		  $contents = file_get_contents($files[$i]);
		  //make class name unique and extend the previous class
		  $class = str_replace(array(BASE_DIR.'/', '/'), array('', '_'), reset(explode('/hooks/', $files[$i])))."__$class";
		  $replace = "class $class extends $last {";
		  //replace and eval
		  eval('?>'.str_replace($search, $replace, $contents));
		  //set $last for the next round
		  $last = $class;
	  }

		 //return the base model if no others
		 if ($count == 0) $class = $last;

		 $hooks[$hook_key] = $class;
	 }

		$class = $hooks[$hook_key];

		//instantiate save the object
		return new $class();
	}
}
