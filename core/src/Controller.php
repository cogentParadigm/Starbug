<?php
namespace Starbug\Core;
use \ReflectionMethod;
class Controller {

	public $template = "auto";
	public $auto_render = true;
	public $request;
	public $response;
	public $routes = array();


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
		$args = array();
		if (method_exists($this, $action)) {
			$reflection = new ReflectionMethod($this, $action);
			$parameters = $reflection->getParameters();
			foreach ($parameters as $parameter) {
				$name = $parameter->getName();
				if (isset($arguments[$name])) $args[] = $arguments[$name];
				else if ($parameter->isDefaultValueAvailable()) $args[] = $parameter->getDefaultValue();
			}
		}
		call_user_func_array(array($this, $action), $args);
	}

	function assign($key, $value = null) {
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

	function start(RequestInterface $request, ResponseInterface $response) {
		$this->request = $request;
		$this->response = $response;
		$this->init();
	}

	function finish() {
		return $this->response;
	}

	public function url($path = "", $absolute = false) {
		return $this->request->getURL()->build($path, $absolute);
	}
	/**
	 * redirect to another page
	 * @ingroup routing
	 * @param string $url the url to redirect to
	 * @param int $delay number of seconds to wait before redirecting (default 0)
	 */
	function redirect($url) {
		$this->response->redirect($this->url($url, true));
	}

	/**
	 * if an unknown action is called, trigger a missing response
	 */
	function __call($name, $arguments) {
		$this->missing();
	}
}
