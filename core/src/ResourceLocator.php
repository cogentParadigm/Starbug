<?php
namespace Starbug\Core;
class ResourceLocator implements ResourceLocatorInterface {

	private $base_directory;
	private $modules;

	function __construct($base_directory = "", $modules = array()) {
		$this->base_directory = $base_directory;
		$this->modules = $modules;
	}

	public function get($mid) {
		return $this->modules[$mid];
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
			if (file_exists($target)) $paths[$mid] = $target;
		}
		return $paths;
	}

	function className($class, $suffix = false) {
		if (false === strpos($class, "\\")) {
			if (false !== $suffix) {
				$class = ucwords($class).$suffix;
			}
			for (end($this->modules); ($mid = key($this->modules)) !== null; prev($this->modules)){
				if (class_exists($mid."\\".$class)) return $mid."\\".$class;
			}
		}
		return $class;
	}
}
