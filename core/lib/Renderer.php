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
	var $vars = array();
	/**
	 * @var string view directory
	 */
	var $prefix = "";
	/**
	 * @var string relative path from the view directory without file extension
	 */	
	var $path = "";

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
	function get_path($path, $scope="") {
		efault($scope, "templates");
		if ($scope == "views" && empty($path)) return request("file");
		$path = $scope."/".$path.".php";
		if (file_exists($this->prefix.$path)) return $this->prefix.$path;
		else if (file_exists("app/themes/".request("theme")."/".$path)) return "app/themes/".request("theme")."/".$path;
		else return "core/".$this->prefix.$path;
	}
	/**
	 * assign a variable
	 * @param string $key variable name
	 * @param string $value variable value
	 */
	function assign($key, $value) {
		$this->vars[$key] = $value;
	}
	/**
	 * render a template
	 * @param string $path relative path to the template from the view directory without file extension
	 */
	function render($paths=array(""), $scope="") {
		global $sb;
		global $request;
		//resolve path
		if (!is_array($paths)) $paths = array($paths);
		$this->path = reset($paths);
		while (!file_exists($filename = $this->get_path($this->path, $scope)) && $this->path) $this->path = next($paths);
		//extract vars
		extract($this->vars);
		//render target
		if (file_exists($filename)) {
			$output = file_get_contents($filename);
			$output = str_replace(array("<? ", "<?\n", "<?="), array("<?php ", "<?php\n", "<?php echo"), $output);
			eval("?>".$output);
		} else die("template not found: ".implode("\n", $paths));
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
