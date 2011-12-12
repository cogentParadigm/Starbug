<?php
$sb->provide("core/lib/Controller");
class Controller {

	function __construct() {
		$this->init();
	}
	
	function init() {
	
	}
	
	function default() {
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

}
?>
