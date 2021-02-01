<?php
namespace Starbug\Core;

use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ControllerMiddleware implements MiddlewareInterface {

  protected $container;

  public function __construct(Container $container) {
    $this->container = $container;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $this->container->set("Psr\Http\Message\ServerRequestInterface", $request);
    $route = $request->getAttribute("route");
    $arguments = $route->getOptions();
    return $this->dispatch($route->getController(), $arguments);
  }
  public function dispatch($controller, $arguments = []): ResponseInterface {
    return $this->container->call($controller, $arguments);
  }
}
