<?php
namespace Starbug\Users\Impersonate;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Routing\Controller;

class Logout extends Controller {
  public function __construct(SessionHandlerInterface $session, $cookiePath = "/") {
    $this->session = $session;
    $this->cookiePath = $cookiePath;
  }
  public function __invoke() {
    if (isset($_COOKIE["iuid"])) {
      // ending an impersonation returns to the users list, still signed in as an admin
      setcookie("iuid", null, 0, $this->cookiePath, null, true, true);
      return $this->redirect("admin/users");
    }
    $this->session->destroy();
    return $this->redirect("");
  }
}
