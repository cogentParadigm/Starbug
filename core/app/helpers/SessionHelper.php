<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;

class SessionHelper {
  public function __construct(
    protected SessionHandlerInterface $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
