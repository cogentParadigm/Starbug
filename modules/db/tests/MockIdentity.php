<?php
namespace Starbug\Db\Tests;

use Starbug\Core\Identity;

class MockIdentity extends Identity {
  public function __construct() {
    // We must extend the constructor to get rid of the dependency on ModelFactoryInterface.
  }
  protected $user = ["id" => 2, "password" => ""];
}
