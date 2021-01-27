<?php
namespace Starbug\Core;

use Starbug\Core\Routing\RouterInterface;

class ApiRoutingController extends Controller {
  public function __construct(ControllerFactoryInterface $controllers, RouterInterface $router, ApiRequest $api) {
    $this->controllers = $controllers;
    $this->router = $router;
    $this->api = $api;
  }
  public function response($controller, $action, $format) {
    $this->api->setFormat($format);
    $controller = $this->controllers->get("Api".ucwords($controller));
    $controller->setApi($this->api);
    return $controller->handle($this->request->withAttribute("action", $action));
  }
}
