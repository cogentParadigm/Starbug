<?php
namespace Starbug\Core;

use Starbug\Auth\Http\CsrfHandlerInterface;

class CsrfHelper {
  public function __construct(
    protected CsrfHandlerInterface $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
