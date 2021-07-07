<?php
namespace Starbug\Users;

use Starbug\Auth\SessionHandlerInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\Table;
use Starbug\Db\Schema\SchemerInterface;

class Users extends Table {

  public function __construct(DatabaseInterface $db, SchemerInterface $schemer, SessionHandlerInterface $session) {
    parent::__construct($db, $schemer);
    $this->session = $session;
  }

  public function delete($user) {
    $this->store(["id" => $user['id'], "deleted" => "1"]);
  }

  /**
   * A function for new users to register themselves
   */
  public function register($user) {
    $this->store(["email" => $user['email'], "password" => $user['password'], "password_confirm" => $user['password_confirm'], "groups" => "user"]);
    if (!$this->errors()) {
      $this->login(["email" => $user['email'], "password" => $user['password']]);
    }
  }

  /**
   * A function for current users to update their profile
   */
  public function updateProfile($profile) {
    // force the user to enter their current password to update their profile
    // validate it by authenticating the sessiong against the entry
    if ($this->session->authenticate($profile['id'], $profile['current_password'])) {
      $this->store(["id" => $profile['id'], "email" => $profile['email'], "password" => $profile['password'], "password_confirm" => $profile['password_confirm']]);
    } else {
      $this->error("Your credentials could not be authenticated.", "current_password");
    }
  }
}
