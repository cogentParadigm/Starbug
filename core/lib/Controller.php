<?php
class Controller {

	public $template = "auto";
	public $auto_render = true;
	public $request;
	public $response;
	public $validators = array();

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
	function action($action = "", $arguments = array()) {
		if (empty($action)) $action = "default_action";
		call_user_func_array(array($this, $action), $arguments);
	}

	function assign($key, $value=null) {
		$this->response->assign($key, $value);
	}

	/**
	 * set the view to render for this request
	 */
	function render($path = "", $params = array(), $options = array("scope" => "views")) {
		$this->response->capture($path, $params, $options);
	}

	/**
	 * return a forbidden response
	 */
	function forbidden() {
		$this->response->forbidden();
		$this->render("forbidden");
	}

	/**
	 * return a missing response
	 */
	function missing() {
		$this->response->missing();
		$this->render("missing");
	}

	function start(Request $request, Response $response) {
		$this->request = $request;
		$this->response = $response;
	}

	function finish() {
		return $this->response;
	}

	/**
	 * if an unknown action is called, trigger a missing response
	 */
	function __call($name, $arguments) {
		$this->missing();
	}

}
?>
