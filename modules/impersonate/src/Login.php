<?php
namespace Starbug\Users\Impersonate;

use Starbug\Auth\IdentityRepositoryInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Db\Operation\Save;
use Starbug\Db\DatabaseInterface;

class Login extends Save {
  protected $model = "users";
  public function __construct(
    DatabaseInterface $db,
    SessionHandlerInterface $session,
    IdentityRepositoryInterface $users,
    $cookiePath = "/"
  ) {
    parent::__construct($db);
    $this->session = $session;
    $this->users = $users;
    $this->cookiePath = $cookiePath;
  }
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $user = $this->users->getIdentity($data["id"]);
    if (empty($user)) {
      $this->db->error("The user was not found.", "global", "users");
    } elseif (!$this->session->loggedIn("admin")) {
      $this->db->error("Permission denied.", "global", "users");
    } else {
      setcookie("iuid", $data["id"], 0, $this->cookiePath, null, true, true);
    }
      return $this->getErrorState($state);
  }
}
