<?php
namespace Starbug\Core;

use Starbug\Core\Routing\RouterInterface;

class AdminController extends Controller {
  public function __construct(ControllerFactoryInterface $controllers, RouterInterface $router) {
    $this->controllers = $controllers;
    $this->router = $router;
  }
  public function defaultAction() {
    if (count($this->request->getComponents()) == 1) {
      return $this->render("admin.html");
    }
    $name = $this->request->getComponent(1);
    $controller = $this->controllers->get("Admin".ucwords($name));
    $controller->start($this->request, $this->response);
    $action = $this->request->getComponent(2);
    $arguments = [];
    if (isset($controller->routes[$action])) {
      $template = $controller->routes[$action];
      if (false === ($values = $this->router->validate($this->request, ['path' => 'admin/'.$name.'/'.$action], $template))) {
        $action = 'missing';
      } elseif (is_array($values)) {
        $arguments = $values;
      }
    }
    $controller->action($action, $arguments);
    $this->response = $controller->finish();
  }
}
