<?php
namespace Starbug\Core;

use Starbug\Http\RequestInterface;

interface ApplicationInterface {
  /**
   * An application must simply handle requests by returning a response object.
   *
   * @param RequestInterface $request the request object
   *
   * @return Response the response object
   */
  public function handle(RequestInterface $request);
}
