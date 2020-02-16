<?php
namespace Starbug\Core\Routing;

use Starbug\Http\RequestInterface;

interface RouteStorageInterface {
  public function getRoute(RequestInterface $request);
}
