<?php
namespace Starbug\Users\Operation;

use Starbug\Db\DatabaseInterface;
use Starbug\Auth\SessionHandlerInterface;
use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;

class UpdateProfile extends Save {
  protected $model = "users";
  public function __construct(DatabaseInterface $db, SessionHandlerInterface $session) {
    $this->db = $db;
    $this->session = $session;
  }
  public function handle(array $profile, BundleInterface $state): BundleInterface {
    // force the user to enter their current password to update their profile
    // validate it by authenticating the sessiong against the entry
    if ($this->session->authenticate($profile['id'], $profile['current_password'])) {
      $this->db->store("users", [
        "id" => $profile['id'],
        "email" => $profile['email'],
        "password" => $profile['password'],
        "password_confirm" => $profile['password_confirm']
      ]);
    } else {
      $this->db->error("Your credentials could not be authenticated.", "current_password", "users");
    }
    return $this->getErrorState($state);
  }
}
