<?php
namespace Starbug\Core\Tests\Auth;

use Starbug\Core\Identity;

class MockIdentity extends Identity {
  public function __construct() {
    $this->users = [];
  }

  public function addUser($user) {
    $this->users[$user["id"]] = $user;
  }

  public function loadUser($id) {
    return $this->users[$id];
  }
}
