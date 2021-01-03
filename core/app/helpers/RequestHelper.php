<?php
namespace Starbug\Core;

use Psr\Http\Message\ServerRequestInterface;

class RequestHelper {
  public function __construct(ServerRequestInterface $request) {
    $this->target = $request;
  }
  public function helper() {
    return $this->target;
  }
}
