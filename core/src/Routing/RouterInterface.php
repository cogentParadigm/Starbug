<?php
namespace Starbug\Core\Routing;
use Starbug\Core\RequestInterface;
interface RouterInterface {
	/**
	 * a router must identify a controller from a Request
	 * @param RequestInterface $request the request object
	 * @return array the controller information using the following keys:
	 *										- controller: the controller name
	 *										- action: the action name
	 *										- arguments: the arguments
	 */
	public function route(RequestInterface $request);
	public function addFilter(RouteFilterInterface $filter);
}
