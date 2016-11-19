<?php
namespace Starbug\Core\Routing;
use Starbug\Core\RequestInterface;
interface RouteStorageInterface {
	public function addRoute($path, $route);
	public function addRoutes($routes);
	public function getRoute(RequestInterface $request);
}
?>
