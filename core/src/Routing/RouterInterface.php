<?php
namespace Starbug\Core\Routing;

use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface {
  /**
   * A router must identify a controller from a Request
   *
   * @param ServerRequestInterface $request the request object
   *
   * @return array the controller information using the following keys:
   *                    - controller: the controller name
   *                    - action: the action name
   *                    - arguments: the arguments
   */
  public function route(ServerRequestInterface $request);
  public function addFilter(RouteFilterInterface $filter);
}
