<?php
namespace Starbug\Core\Routing;

interface AccessInterface {
  public function hasAccess(Route $route);
}
