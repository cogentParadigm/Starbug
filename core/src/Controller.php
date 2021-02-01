<?php
namespace Starbug\Core;

use GuzzleHttp\Psr7\Response;
use Starbug\Http\TemplatedResponse;

class Controller {

  protected $parameters = [];

  /**
   * Assign a variable
   */
  public function assign($key, $value = null) {
    $merge = is_array($key) ? $key : [$key => $value];
    $this->parameters = $merge + $this->parameters;
  }

  /**
   * Return a templated response.
   */
  public function render($path = "", $params = [], $options = []) {
    return new TemplatedResponse($path, $params + $this->parameters, $options);
  }

  /**
   * Return a forbidden response.
   */
  public function forbidden($to = false) {
    if (false !== $to) return $this->redirect("login?to=".$to);
    return $this->redirect("login");
  }

  /**
   * Return a missing response.
   */
  public function missing() {
    return $this->render("missing.html")->withStatus(404);
  }

  /**
   * Return a redirect response.
   */
  public function redirect($path, $status = 302) {
    return new Response($status, ["Location" => $path]);
  }
}
