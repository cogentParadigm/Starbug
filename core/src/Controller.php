<?php
namespace Starbug\Core;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionMethod;
use Starbug\Http\ResponseBuilderInterface;

class Controller {

  /**
   * PSR-7 Server Request
   *
   * @var ServerRequestInterface
   */
  protected $request;
  /**
   * PSR-7 Response builder
   *
   * @var ResponseBuilderInterface
   */
  protected $response;

  public function setResponseBuilder(ResponseBuilderInterface $builder) {
    $this->response = $builder;
  }


  public function init() {
  }

  /**
   * Every controller has a default action, used when no action is specified.
   */
  public function defaultAction() {
    $this->missing();
  }

  /**
   * Run a controller action.
   *
   * @param string $action - the action to run, an empty string will run defaultAction
   * @param string $arguments - arguments to pass to the action
   */
  public function action($action = "", $arguments = []) {
    if (empty($action)) {
      $action = "defaultAction";
    } else {
      $action = $this->formatActionName($action);
    }
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

  /**
   * Assign a variable
   */
  public function assign($key, $value = null) {
    $this->response->assign($key, $value);
  }

  /**
   * Set the view to render for this request.
   */
  public function render($path = "", $params = [], $options = []) {
    $this->response->render($path, $params, $options);
  }

  /**
   * Return a forbidden response.
   */
  public function forbidden() {
    $this->response->redirect("login?to=".$this->request->getUri()->getPath());
  }

  /**
   * Return a missing response.
   */
  public function missing() {
    $this->response->create(404);
    $this->render("missing.html");
  }

  /**
   * Generate a response
   */
  public function handle(ServerRequestInterface $request, $route = []) : ResponseInterface {
    $route += ["action" => "", "arguments" => []];
    $this->request = $request;
    $this->response->assign("route", $route);
    if (!empty($route["format"])) {
      $this->response->setFormat($route["format"]);
    }
    if (!empty($route["template"])) {
      $this->response->setTemplate($route["template"]);
    }
    $this->init();
    $this->action($route["action"], $route["arguments"]);
    return $this->response->getResponse();
  }

  /**
   * If an unknown action is called, trigger a missing response.
   */
  public function __call($name, $arguments) {
    $this->missing();
  }

  /**
   * Convert a URL component with dashes to camel case format.
   *
   * @param string $segment the path segment.
   *
   * @return string the camel case converted name
   */
  protected function formatActionName($segment) {
    return str_replace(" ", "", ucwords(str_replace("-", " ", $segment)));
  }
}
