<?php
namespace Starbug\Users\Operation;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\Operation\Save;

class Login extends Save {
  public function __construct(DatabaseInterface $db, SessionHandlerInterface $session) {
    $this->db = $db;
    $this->session = $session;
  }
  public function handle(array $login, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    if ($user = $this->session->authenticate(["email" => $login['email']], $login['password'])) {
      $this->session->createSession($user);
      $this->db->store("users", ["id" => $user->getId(), "last_visit" => date("Y-m-d H:i:s")]);
    } else {
      $this->db->error("That email and password combination was not found.", "email", "users");
    }
    return $this->getErrorState($state);
  }
}
