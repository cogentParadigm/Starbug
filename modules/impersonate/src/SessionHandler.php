<?php
namespace Starbug\Users\Impersonate;

use Starbug\Auth\IdentityRepositoryInterface;
use Starbug\Auth\SessionHandlerHook;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Auth\SessionInterface;

class SessionHandler implements SessionHandlerHook {
  // @var IdentityRepositoryInterface
  protected $users;
  public function __construct(IdentityRepositoryInterface $users) {
    $this->users = $users;
  }
  public function startSession(SessionHandlerInterface $handler) {
    $iuid = $_COOKIE["iuid"] ?? false;
    if ($handler->loggedIn("admin") && !empty($iuid) && is_numeric($iuid)) {
      if ($id = $this->users->getIdentity($iuid)) {
        $handler->getSession()->setIdentity($id);
      }
    }
  }
  public function createSession(SessionHandlerInterface $handler, SessionInterface $session, bool $persist, bool $activate) {
    // Do nothing.
  }

  public function destroy(SessionHandlerInterface $handler) {
    // Do nothing.
  }
}
