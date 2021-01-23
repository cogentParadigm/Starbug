<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;

class SessionHelper {
  public function __construct(SessionHandlerInterface $session) {
    $this->target = $session;
  }
  public function helper() {
    return $this->target;
  }
}
