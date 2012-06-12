<?php
$sb->provide("core/lib/Controller");
class Controller {

	function __construct() {
		$this->init();
	}
	
	function init() {
	
	}
	
	function default_action() {
		render("html");
	}

	function render($path="") {
		if (!empty($path)) request()->file = locate_view($path);
		render("html");
	}

	function forbidden() {
		request()->forbidden();
		render("html");
	}

	function missing() {
		request()->missing();
		render("html");
	}
	
	function __call($name, $arguments) {
		$this->missing();
	}

}
?>
