<?php
namespace Starbug\Core\Routing;

class Configuration {
  protected $isBuilt = false;
  protected $providers = [];
  public function __construct(Route $root = null) {
    $this->root = $root ?? new Route("/");
  }
  public function addProvider(RouteProviderInterface $provider) {
    $this->providers[] = $provider;
    return $this;
  }
  public function addProviders($providers) {
    foreach ($providers as $provider) {
      $this->addProvider($provider);
    }
    return $this;
  }
  public function build() {
    if (!$this->isBuilt) {
      $this->invokeProviders("configure", [$this->root]);
      $this->isBuilt = true;
    }
  }
  public function getRoutes() {
    $this->build();
    return $this->flatten([$this->root]);
  }
  protected function invokeProviders($method, $arguments = []) {
    foreach ($this->providers as $provider) {
      call_user_func_array([$provider, $method], $arguments);
    }
  }
  protected function flatten($routes) {
    $flat = [];
    foreach ($routes as $route) {
      if ($route->getController() != null) {
        $flat[] = $route;
      }
      $flat = array_merge($flat, $this->flatten($route->getRoutes()));
    }
    return $flat;
  }
}
