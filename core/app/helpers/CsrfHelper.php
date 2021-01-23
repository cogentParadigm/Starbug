<?php
namespace Starbug\Core;

use Starbug\Auth\Http\CsrfHandlerInterface;

class CsrfHelper {
  public function __construct(CsrfHandlerInterface $target) {
    $this->target = $target;
  }
  public function helper() {
    return $this->target;
  }
}
