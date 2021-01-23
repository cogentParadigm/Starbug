<?php
namespace Starbug\Core\Routing;

use Starbug\Auth\SessionHandlerInterface;

class Access implements AccessInterface {
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function hasAccess($route) {
    if (empty($route["groups"])) return true;
    if (!is_array($route["groups"])) {
      $route["groups"] = [$route["groups"]];
    }
    foreach ($route["groups"] as $group) {
      if ($this->session->loggedIn($group)) return true;
    }
    return false;
  }
}
