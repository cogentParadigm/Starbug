<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Renderer.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Renderer
 */
/**
 * @defgroup Renderer
 * The starbug templating engine, see @link core/global/templates.php for global functions
 * @ingroup lib
 */
$sb->provide("core/lib/Renderer");
/**
 * Renderer class. assign/render style templating engine
 * @ingroup Renderer
 */
class Renderer {
	/**
	 * @var array assigned variables
	 */
	var $vars = array("global" => array());
	/**
	 * @var string view directory
	 */
	var $prefix = "";
	/**
	 * @var string relative path from the view directory without file extension
	 */	
	var $path = "";
	var $active_scope = "global";
	var $directory_scope = false;
	/**
	 * constructor. initializes variables
	 * @param string $prefix the view directory
	 * @param string $path relative path from the view directory without file extension
	 */
	function __construct($prefix="app/", $path="") {
		$this->prefix = $prefix;
		$this->path = $path;
	}
	/**
	 * get full path
	 * @param string $path variable name
	 */
	function get_path($path, $scope) {
		if ($scope == "views" && empty($path)) return request("file");
		$path = ($this->directory_scope) ? $scope."/".$path.".php" : "templates/".$path.".php";
		if (file_exists($this->prefix.$path)) return $this->prefix.$path;
		else if (file_exists("app/themes/".request("theme")."/".$path)) return "app/themes/".request("theme")."/".$path;
		else return "core/".$this->prefix.$path;
	}
	/**
	 * assign a variable
	 * @param string $key variable name
	 * @param string $value variable value
	 */
	function assign($key, $value, $scope="") {
		efault($scope, $this->active_scope);
		efault($scope, "global");
		$scope = "global";
		efault($this->vars[$scope], array());
		$this->vars[$scope][$key] = $value;
	}
	/**
	 * render a template
	 * @param string $path relative path to the template from the view directory without file extension
	 */
	function render($path=array(""), $scope="") {
		global $sb;
		global $request;
		//set scope
		efault($scope, $this->active_scope);
		efault($scope, "global");
		$this->directory_scope = (file_exists($this->prefix.$scope) || file_exists("core/".$this->prefix.$scope));
		if (!$this->directory_scope) {
			$scope = "global";
			$old_scope = $this->active_scope;
			$this->active_scope = $scope;
		}
		//resolve path
		if (!is_array($path)) $path = array($path);
		$this->path = reset($path);
		while (!file_exists($filename = $this->get_path($this->path, $scope)) && $this->path) $this->path = next($path);
		//extract vars
		if (($scope != "global") && !empty($this->vars[$scope])) extract($this->vars[$scope]);
		extract($this->vars["global"]);
		//render target
		if (file_exists($filename)) {
			$output = file_get_contents($filename);
			$output = str_replace(array("<? ", "<?\n", "<?="), array("<?php ", "<?php\n", "<?php echo"), $output);
			eval("?>".$output);
		} else error("template not found: ".implode("\n", $path), $scope, "renderer");
		//reset scope
		if ($this->directory_scope) $this->directory_scope = false;
		else $this->active_scope = $old_scope;
	}
	
	/**
	 * capture a rendered template
	 * @param string $path relative path to the template from the view directory without file extension
	 */
	function capture($path="", $scope) {
		ob_start();
		$this->render($path, $scope);
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}	

}
/**
 * global Renderer instance
 * @ingroup global
 */
global $renderer;
$renderer = new Renderer();
?>
