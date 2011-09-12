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
	function __construct($prefix="app/views/", $path="") {
		$this->prefix = $prefix;
		$this->path = $path;
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
	function render($path="") {
		global $sb;
		global $request;
		//$this->assign("this", $request);
		if (!empty($path)) $this->path = $path;
		extract($this->vars);
		$output = file_get_contents($this->prefix."templates/".$this->path.".php");
		$output = str_replace(array("<? ", "<?="), array("<?php ", "<?php echo"), $output);
		eval("?>".$output);
	}

}
global $renderer;
$renderer = new Renderer();
?>
