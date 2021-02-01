<?php
namespace Starbug\Core\Routing;

use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface {
  protected $storage = [];
  protected $aliasStorage = [];
  protected $filters = [];

  public function addStorage(RouteStorageInterface $storage) {
    $this->storage[] = $storage;
  }

  public function addAliasStorage(AliasStorageInterface $storage) {
    $this->aliasStorage[] = $storage;
  }

  public function addFilter(RouteFilterInterface $filter) {
    $this->filters[] = $filter;
  }

  public function getRoute(ServerRequestInterface $request): Route {
    if ($path = $this->resolveAlias($request)) {
      $request = $request->withUri($request->getUri()->withPath($path));
    }
    foreach ($this->storage as $storage) {
      if ($route = $storage->getRoute($request)) {
        return $this->filterRoute($route, $request);
      }
    }
    return $this->filterRoute(new Route($path, ["Starbug\\Core\\MainController", "missing"], ["arguments" => []]), $request);
  }
  /**
   * A router must identify a controller from a Request
   *
   * @param Request $request the request object
   *
   * @return array the controller information using the following keys:
   *                    - controller: the controller name
   *                    - action: the action name
   *                    - arguments: the arguments
   */
  public function route(ServerRequestInterface $request): Route {
    return $this->getRoute($request);
  }

  protected function resolveAlias(ServerRequestInterface $request) {
    foreach ($this->aliasStorage as $storage) {
      if ($path = $storage->getPath($request)) {
        return $path;
      }
    }
    return false;
  }

  protected function filterRoute($route, ServerRequestInterface $request) {
    foreach ($this->filters as $filter) {
      $route = $filter->filterRoute($route, $request);
    }
    return $route;
  }
}
