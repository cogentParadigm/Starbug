<?php
$sb->provide("core/lib/Controller");
class Controller {
	
	var $template = "auto";
	var $auto_render = true;

	function __construct() {
		$this->init();
	}
	
	function init() {
	
	}

	/**
	 * Every controller has a default action, used when no action is specified.
	 */
	function default_action() {
		
	}
	
	/**
	 * run a controller action
	 * @param string $action - the action to run, an empty string will run default_action
	 * @param string $arguments - arguments to pass to the action
	 */
	function action($action="", $arguments=array()) {
		if (empty($action)) $action = "default_action";
		call_user_func_array(array($this, $action), $arguments);
		if ($this->auto_render) render(($this->template == "auto") ? request()->format : $this->template);
	}

	/**
	 * forward the request to another controller
	 * @param string $controller
	 * @param string $action
	 * @param array $args
	 */
	function forward($controller, $action, $args=array()) {
		$this->auto_render = false;
		controller($controller)->action($action, $args);
	}

	/**
	 * set the view to render for this request
	 */
	function render($path="") {
		if (!empty($path)) request()->file = locate_view($path);
	}

	/**
	 * return a forbidden response
	 */
	function forbidden() {
		request()->forbidden();
	}

	/**
	 * return a missing response
	 */
	function missing() {
		request()->missing();
	}

	/**
	 * if an unknown action is called, trigger a missing response
	 */
	function __call($name, $arguments) {
		$this->missing();
	}

}
?>
