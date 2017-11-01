<?php
namespace Starbug\Core\Routing;
use Starbug\Core\RequestInterface;
class MemoryRouteStorage implements RouteStorageInterface {
	protected $routes = [];
	public function __construct(AccessInterface $access) {
		$this->access = $access;
	}
	public function addRoute($path, $route) {
		$this->routes[$path] = $route;
	}
	public function addRoutes($routes) {
		foreach ($routes as $path => $route) {
			$this->addRoute($path, $route);
		}
	}
	public function getRoute(RequestInterface $request) {
		$route = array("controller" => "main", "action" => "missing", "arguments" => array());
		$paths = $this->expand($request->getPath());
		foreach ($paths as $path) {
			if (!empty($this->routes[$path])) {
				$route = $this->routes[$path] + ["path" => $path];
				if ($this->access->hasAccess($route)) {
					return $route;
				} else {
					$route = array("controller" => "main", "action" => "forbidden", "arguments" => array());
				}
			}
		}
		return $route;
	}
	protected function expand($path) {
		$expanded = array();
		$parts = explode("/", $path);
		foreach ($parts as $idx => $part) {
			if ($idx) $part = $expanded[0].'/'.$part;
			array_unshift($expanded, $part);
		}
		return $expanded;
	}
}
