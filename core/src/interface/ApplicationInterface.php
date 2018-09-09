<?php
namespace Starbug\Core;

interface ApplicationInterface {
  /**
   * an application must simply handle requests by returning a response object
   * @param RequestInterface $request the request object
   * @return Response the response object
   */
  public function handle(RequestInterface $request);
}
