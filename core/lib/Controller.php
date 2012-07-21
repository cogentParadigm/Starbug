<?php
$sb->provide("core/lib/Controller");
class Controller {
	
	var $template = "html";
	var $auto_render = true;

	function __construct() {
		$this->init();
	}
	
	function init() {
	
	}
	
	function default_action() {
		
	}

	function render($path="") {
		if (!empty($path)) request()->file = locate_view($path);
	}

	function forbidden() {
		request()->forbidden();
	}

	function missing() {
		request()->missing();
	}
	
	function __call($name, $arguments) {
		$this->missing();
	}

}
?>
