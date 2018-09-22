<?php
namespace Starbug\Core;

use \ReflectionMethod;

class Controller {

  public $template = "auto";
  public $auto_render = true;
  public $request;
  public $response;
  public $routes = [];


  public function init() {
  }

  /**
   * Every controller has a default action, used when no action is specified.
   */
  public function default() {
    $this->missing();
  }

  /**
   * Run a controller action.
   *
   * @param string $action - the action to run, an empty string will run default_action
   * @param string $arguments - arguments to pass to the action
   */
  public function action($action = "", $arguments = []) {
    if (empty($action)) $action = "default_action";
    $args = [];
    if (method_exists($this, $action)) {
      $reflection = new ReflectionMethod($this, $action);
      $parameters = $reflection->getParameters();
      foreach ($parameters as $parameter) {
        $name = $parameter->getName();
        if (isset($arguments[$name])) $args[] = $arguments[$name];
        elseif ($parameter->isDefaultValueAvailable()) $args[] = $parameter->getDefaultValue();
      }
    }
    call_user_func_array([$this, $action], $args);
  }

  public function assign($key, $value = null) {
    $this->response->assign($key, $value);
  }

  /**
   * Set the view to render for this request.
   */
  public function render($path = "", $params = [], $options = ["scope" => "views"]) {
    $this->response->capture($path, $params, $options);
  }

  /**
   * Return a forbidden response.
   */
  public function forbidden() {
    $this->response->forbidden();
    $this->render("forbidden.html");
  }

  /**
   * Return a missing response.
   */
  public function missing() {
    $this->response->missing();
    $this->render("missing.html");
  }

  public function start(RequestInterface $request, ResponseInterface $response) {
    $this->request = $request;
    $this->response = $response;
    $this->init();
  }

  public function finish() {
    return $this->response;
  }

  public function url($path = "", $absolute = false) {
    return $this->request->getURL()->build($path, $absolute);
  }
  /**
   * Redirect to another page.
   *
   * @param string $url the url to redirect to
   * @param int $delay number of seconds to wait before redirecting (default 0)
   */
  public function redirect($url) {
    $this->response->redirect($this->url($url, true));
  }

  /**
   * If an unknown action is called, trigger a missing response.
   */
  public function __call($name, $arguments) {
    $this->missing();
  }
}
