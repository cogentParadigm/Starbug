<?php
namespace Starbug\Core;

use Starbug\Core\Routing\RouterInterface;

class ApiRoutingController extends Controller {
  public function __construct(ControllerFactoryInterface $controllers, RouterInterface $router, ApiRequest $api) {
    $this->controllers = $controllers;
    $this->router = $router;
    $this->api = $api;
  }
  public function response() {
    if (count($this->request->getComponents()) == 1) {
      $this->response = "[]";
      return;
    }
    $name = $this->request->getComponent(1);
    $controller = $this->controllers->get("Api".ucwords($name));
    $controller->setApi($this->api);
    $controller->start($this->request, $this->response);
    $action = $this->request->getComponent(2);
    $arguments = [];
    if (isset($controller->routes[$action])) {
      $template = $controller->routes[$action];
      if (false === ($values = $this->router->validate($this->request, ['path' => 'api/'.$name.'/'.$action], $template))) {
        $action = 'missing';
      } elseif (is_array($values)) {
        $arguments = $values;
      }
    }
    $controller->action($action, $arguments);
    $this->response = $controller->finish();
  }
}
