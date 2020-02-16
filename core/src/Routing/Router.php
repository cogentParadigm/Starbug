<?php
namespace Starbug\Core\Routing;

use Starbug\Http\RequestInterface;

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

  public function getRoute(RequestInterface $request) {
    if ($path = $this->resolveAlias($request)) {
      $request->setPath($path);
    }
    foreach ($this->storage as $storage) {
      if ($route = $storage->getRoute($request)) {
        return $this->filterRoute($route, $request);
      }
    }
    return $this->filterRoute(["controller" => "main", "action" => "missing", "arguments" => []], $request);
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
  public function route(RequestInterface $request) {
    $route = $this->getRoute($request);
    if (empty($route['controller']) && !empty($route['type'])) {
      $route = array_replace(['controller' => $route['type'], 'action' => 'show'], $route);
      $route['controller'] = $route['type'];
      if (empty($route['action'])) $route['action'] = 'show';
    }
    return $route;
  }

  protected function resolveAlias(RequestInterface $request) {
    foreach ($this->aliasStorage as $storage) {
      if ($path = $storage->getPath($request)) {
        return $path;
      }
    }
    return false;
  }

  protected function filterRoute($route, RequestInterface $request) {
    foreach ($this->filters as $filter) {
      $route = $filter->filterRoute($route, $request);
    }
    return $route;
  }
}
