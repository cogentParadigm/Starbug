<?php
namespace Starbug\Core\Routing;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface {
  public function addStorage(RouteStorageInterface $storage);
  public function addAliasStorage(AliasStorageInterface $storage);
  public function addFilter(RouteFilterInterface $filter);
  public function route(ServerRequestInterface $request): Route;
  public function resolveParameters(Route $route, ServerRequestInterface $request, $type = "inbound"): Route;
}
