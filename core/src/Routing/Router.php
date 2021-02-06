<?php
namespace Starbug\Core\Routing;

use Exception;
use Invoker\InvokerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface {
  protected $storage = [];
  protected $aliasStorage = [];
  protected $filters = [];
  protected $invoker;

  public function __construct(InvokerInterface $invoker) {
    $this->invoker = $invoker;
  }

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
        if ($route->isForbidden()) {
          return $this->forbidden($request);
        }
        return $route;
      }
    }
    if (in_array($request->getUri()->getPath(), ["/missing"])) {
      throw new Exception("No '{$request->getUri()->getPath()}' route defined.");
    }
    return $this->notFound($request);
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
    $route = $this->getRoute($request);
    $route = $this->resolveParameters($route, $request);
    $route = $this->filterRoute($route, $request);
    return $route;
  }

  public function resolveParameters(Route $route, ServerRequestInterface $request, $type = "inbound"): Route {
    if ($route->hasResolvers($type)) {
      $arguments = $route->getOptions() + ["route" => $route];
      foreach ($route->getResolvers($type) as $key => $value) {
        $arguments[$key] = $this->invoker->call($value["resolver"], $arguments);
        $route->setOption($key, $arguments[$key]);
      }
      if ($route->isNotFound()) {
        return $this->notFound($request);
      }
      if ($route->isForbidden()) {
        return $this->forbidden($request);
      }
    }
    return $route;
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

  protected function notFound(ServerRequestInterface $request) {
    return $this->getRoute($request->withUri($request->getUri()->withPath("/missing")))
      ->setOption("requestUri", $request->getUri());
  }

  protected function forbidden(ServerRequestInterface $request) {
    return $this->getRoute($request->withUri($request->getUri()->withPath("/forbidden")))
      ->setOption("requestUri", $request->getUri());
  }
}
