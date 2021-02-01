<?php
namespace Starbug\Core\Routing;

use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Starbug\Core\Routing\Route;

class ResolutionMiddleware implements MiddlewareInterface {

  /**
   * Dependencies need to resolve route parameters.
   *
   * @param Container $container DI container.
   */
  public function __construct(Container $container) {
    $this->container = $container;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $route = $request->getAttribute("route");
    $this->resolveParameters($route, "inbound");
    $response = $handler->handle($request);
    $this->resolveParameters($route, "outbound");
    return $response;
  }
  protected function resolveParameters(Route $route, $type = "inbound") {
    if ($route->hasResolvers($type)) {
      $arguments = $route->getOptions();
      foreach ($route->getResolvers($type) as $key => $value) {
        $arguments[$key] = $this->container->call($value["resolver"], $arguments);
        $route->setOption($key, $arguments[$key]);
      }
    }
  }
}
