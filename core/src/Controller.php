<?php
namespace Starbug\Core;

use \ReflectionMethod;
use Starbug\Http\RequestInterface;
use Starbug\Http\ResponseInterface;

class Controller {

  public $template = "auto";
  public $auto_render = true;
  public $request;
  public $response;
  protected $output;


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

  public function assign($key, $value = null) {
    $this->output->assign($key, $value);
  }

  /**
   * Set the view to render for this request.
   */
  public function render($path = "", $params = [], $options = []) {
    $options = $options + ["scope" => "views"];
    $this->response->setContent($this->output->capture($path, $params, $options));
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

  public function start(TemplateInterface $output, RequestInterface $request, ResponseInterface $response) {
    $this->output = $output;
    $this->request = $request;
    $this->response = $response;
    $this->init();
  }

  public function finish() {
    return $this->response;
  }

  public function url($path = "", $absolute = false) {
    return $this->request->getUrl()->build($path, $absolute);
  }
  /**
   * Redirect to another page.
   *
   * @param string $url the url to redirect to
   * @param int $delay number of seconds to wait before redirecting (default 0)
   */
  public function redirect($url) {
    $url = $this->url($url, true);
    $this->response->setHeader('Location', $url);
    $this->response->setContent('<script>setTimeout("location.href = \''.$url.'\';");</script>');
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
