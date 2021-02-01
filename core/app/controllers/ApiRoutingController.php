<?php
namespace Starbug\Core;

use Psr\Http\Message\ServerRequestInterface;

class ApiRoutingController extends Controller {
  public function __construct(ControllerFactoryInterface $controllers, ControllerMiddleware $dispatcher, ApiRequest $api) {
    $this->controllers = $controllers;
    $this->dispatcher = $dispatcher;
    $this->api = $api;
  }
  public function __invoke(ServerRequestInterface $request, $controller, $action, $format) {
    $this->api->setFormat($format);
    $controller = $this->controllers->get("Api".ucwords($controller));
    $controller->setApi($this->api);
    return $this->dispatcher->dispatch([$controller, $action], $request->getAttribute("route")->getOptions());
  }
}
