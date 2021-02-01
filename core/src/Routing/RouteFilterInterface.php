<?php
namespace Starbug\Core\Routing;

use Psr\Http\Message\ServerRequestInterface;

interface RouteFilterInterface {
  /**
   * Filter a route.
   *
   * @param array $route the identified route
   * @param RequestInterface $request the request which produced the route
   *
   * @return array the route
   */
  public function filterRoute(Route $route, ServerRequestInterface $request);
}
