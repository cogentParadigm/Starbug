<?php
namespace Starbug\Core;

use Starbug\Http\RequestInterface;

class RequestHelper {
  public function __construct(RequestInterface $request) {
    $this->target = $request;
  }
  public function helper() {
    return $this->target;
  }
}
