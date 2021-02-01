<?php
namespace Starbug\Core\Routing;

interface RouteProviderInterface {
  public function configure(Route $routes);
}
