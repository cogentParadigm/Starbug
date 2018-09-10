<?php
namespace Starbug\Core;

use Starbug\Core\Routing\RouterInterface;

class Application implements ApplicationInterface {

  protected $controllers;
  protected $models;
  protected $router;
  protected $request;
  protected $response;
  protected $config;
  protected $session;

  use \Psr\Log\LoggerAwareTrait;

  /**
   * All the dependencies needed to co-ordinate the application.
   *
   * @param ControllerFactoryInterface $controllers Factory to create controllers.
   * @param ModelFactoryInterface $models Factory to create models.
   * @param RouterInterface $router Router translates paths to controllers.
   * @param SessionHandlerInterface $session Session for authenticated users.
   * @param ResponseInterface $response Response which will be prepared and returned.
   * @param InputFilterInterface $filter Utility for input sanitization.
   */
  public function __construct(
    ControllerFactoryInterface $controllers,
    ModelFactoryInterface $models,
    RouterInterface $router,
    SessionHandlerInterface $session,
    ResponseInterface $response,
    InputFilterInterface $filter
  ) {
    $this->controllers = $controllers;
    $this->models = $models;
    $this->router = $router;
    $this->session = $session;
    $this->response = $response;
    $this->filter = $filter;
  }

  public function handle(RequestInterface $request) {
    $this->session->startSession();
    $permitted = $this->check_post($request->getPost(), $request->getCookies());
    $this->response->assign("request", $request);
    $route = $this->router->route($request);
    foreach ($route as $k => $v) {
      if (!empty($v)) $this->response->{$k} = $v;
    }
    $this->logger->addInfo("Loading ".$route['controller'].' -> '.$route['action']);
    $controller = $this->controllers->get($route['controller']);

    if (isset($controller->routes[$route['action']])) {
      $template = $controller->routes[$route['action']];
      if (false === ($values = $this->router->validate($request, $route, $template))) {
        $route['action'] = 'missing';
      } elseif (is_array($values)) {
        $route['arguments'] = $values;
      }
    }

    if (empty($route['arguments'])) $route['arguments'] = [];

    $controller->start($request, $this->response);
    if ($permitted) $controller->action($route['action'], $route['arguments']);
    else $controller->forbidden();
    $this->response = $controller->finish();
    return $this->response;
  }
  /**
   * Run a model action if permitted.
   *
   * @param string $key The model name.
   * @param string $value The function name.
   * @param array $post The posted data.
   *
   * @return mixed result of function call or nothing.
   */
  protected function post_action($key, $value, $post = []) {
    $this->logger->addInfo("Attempting action ".$key.' -> '.$value);
    if ($object = $this->models->get($key)) {
      return $object->post($value, $post);
    }
  }

  /**
   * Check $_POST['action'] for posted actions and run them through post_action.
   */
  protected function check_post($post, $cookies) {
    if (!empty($post['action']) && is_array($post['action'])) {
      // Validate csrf token for authenticated requests.
      if ($this->session->loggedIn()) {
        $validated = false;
        if (!empty($cookies['oid']) && !empty($post['oid']) && $cookies['oid'] === $post['oid']) $validated = true;
        if (true !== $validated) {
          return false;
        }
      }
      // Execute post actions.
      foreach ($post['action'] as $key => $val) return $this->post_action($this->filter->normalize($key), $this->filter->normalize($val), $post[$key]);
    }
    return true;
  }
}
