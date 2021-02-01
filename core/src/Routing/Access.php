<?php
namespace Starbug\Core\Routing;

use Starbug\Auth\SessionHandlerInterface;

class Access implements AccessInterface {
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function hasAccess(Route $route) {
    $groups = $route->getOption("groups");
    if (empty($groups)) return true;
    if (!is_array($groups)) {
      $groups = [$groups];
    }
    foreach ($groups as $group) {
      if ($this->session->loggedIn($group)) return true;
    }
    return false;
  }
}
