<?php
namespace Starbug\Db\Tests;

use Starbug\Auth\SessionHandler;

class MockSessionHandler extends SessionHandler {
  public function __construct() {
    // no dependencies needed.
  }
  public function loggedIn($group = "") {
    return true;
  }
  public function getUserId() {
    return 2;
  }
}
