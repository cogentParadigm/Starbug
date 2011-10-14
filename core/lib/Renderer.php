<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Renderer.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup core
 */
$sb->provide("core/lib/Renderer");
/**
 * Renderer class. assign/render style templating engine
 * @ingroup core
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
	/**
	 * constructor. initializes variables
	 * @param string $prefix the view directory
	 * @param string $path relative path from the view directory without file extension
	 */
	function __construct($prefix="app/views/", $path="") {
		$this->prefix = $prefix;
		$this->path = $path;
	}
	/**
	 * get full path
	 * @param string $path variable name
	 */
	function get_path($path, $scope) {
		if ($scope == "view") return (empty($path)) ? request("file") : $this->prefix.$path.".php";
		$path = "templates/".$path.".php";
		if (file_exists("app/themes/".request("theme")."/".$path)) return "app/themes/".request("theme")."/".$path;
		else if (file_exists($this->prefix.$path)) return $this->prefix.$path;
		else return "core/".$this->prefix.$path;
	}
	/**
	 * assign a variable
	 * @param string $key variable name
	 * @param string $value variable value
	 */
	function assign($key, $value, $scope="global") {
		efault($this->vars[$scope], array());
		$this->vars[$scope][$key] = $value;
	}
	/**
	 * render a template
	 * @param string $path relative path to the template from the view directory without file extension
	 */
	function render($path="", $scope) {
		global $sb;
		global $request;
		if (!empty($path)) $this->path = $path;
		extract($this->vars["global"]);
		if (empty($scope)) $scope = $this->active_scope;
		else $this->active_scope = $scope;
		if (($scope != "global") && !empty($this->vars[$scope])) extract($this->vars[$scope]);
		$output = file_get_contents($this->get_path($path, $scope));
		$output = str_replace(array("<? ", "<?\n", "<?="), array("<?php ", "<?php\n", "<?php echo"), $output);
		eval("?>".$output);
		$this->active_scope = "global";
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
global $renderer;
$renderer = new Renderer();
?>
