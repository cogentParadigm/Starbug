<?php
namespace Starbug\Core\Routing;

use Psr\Http\Message\ServerRequestInterface;

interface RouteStorageInterface {
  public function getRoute(ServerRequestInterface $request);
}
