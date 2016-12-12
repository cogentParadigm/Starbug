<?php
namespace Starbug\Core\Routing;
use Starbug\Core\RequestInterface;
interface RouteFilterInterface {
	/**
	 * filter a route
	 * @param  array $route the identified route
	 * @param  RequestInterface $request the request which produced the route
	 * @return array the route
	 */
	public function filterRoute($route, RequestInterface $request);
}
?>
