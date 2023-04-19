<?php
namespace Starbug\Core\Routing;

use Starbug\Core\Routing\Traits\RouteProperties;
use Starbug\Core\Routing\Traits\Routes;
use Starbug\Core\Routing\Traits\Operations;
use Starbug\Core\Routing\Traits\Resolvers;
use Starbug\Core\Routing\Traits\Status;

class Route {

  use RouteProperties;
  use Routes;
  use Operations;
  use Resolvers;
  use Status;

  public function __construct($path, $controller = null, $options = [], $parent = null) {
    $this->path = $path;
    $this->controller = $controller;
    $this->options = $options;
    $this->parent = $parent;
  }
}
