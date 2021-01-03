<?php
namespace Starbug\Core;

use Psr\Http\Message\ServerRequestInterface;

interface ApplicationInterface {
  /**
   * An application must simply handle requests by returning a response object.
   *
   * @param RequestInterface $request the request object
   *
   * @return Response the response object
   */
  public function handle(ServerRequestInterface $request);
}
