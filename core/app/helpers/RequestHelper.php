<?php
namespace Starbug\Core;

use Psr\Http\Message\ServerRequestInterface;

class RequestHelper {
  public function __construct(
    protected ServerRequestInterface $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
