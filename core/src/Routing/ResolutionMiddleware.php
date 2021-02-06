<?php
namespace Starbug\Core\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResolutionMiddleware implements MiddlewareInterface {

  protected $router;

  public function __construct(RouterInterface $router) {
    $this->router = $router;
  }

  public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $route = $request->getAttribute("route");
    $response = $handler->handle($request);
    $this->router->resolveParameters($route, $request, "outbound");
    return $response;
  }
}
