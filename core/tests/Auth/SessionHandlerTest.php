<?php
namespace Starbug\Core\Tests\Auth;

use Starbug\Core\SessionHandler;

class SessionHandlerTest extends \PHPUnit_Framework_TestCase {
  public function setUp() {
    $this->storage = new MockSessionStorage();
    $this->user = new MockIdentity();
    $this->session = new SessionHandler($this->storage, $this->user);
  }

  public function testLoggedOut() {
    $this->session->startSession();

    $this->assertFalse($this->user->loggedIn());
    $this->assertFalse($this->session->loggedIn());
  }

  public function testLoggedIn() {
    $id = rand(1, 100);
    $this->user->addUser(["id" => $id]);
    $this->storage->createSession($id, 0);

    $this->assertFalse($this->session->loggedIn());

    $this->session->startSession();

    $this->assertTrue($this->session->loggedIn());
    $this->assertEquals($this->user->userinfo("id"), $id);
  }

  public function testPasswordValidation() {
    $passwords = [
      "5ba8036df3b35",
      "5ba8036df3b74",
      "5ba8036df3ba3",
      "5ba8036df3bb6",
      "5ba8036df3bc6"
    ];
    $badPasswords = [
      "5ba803cc36bfc",
      "5ba803cc36c20",
      "5ba803cc36c32",
      "5ba803cc36c45",
      "5ba803cc36c56"
    ];
    foreach ($passwords as $idx => $password) {
      $hash = $this->session->hashPassword($password);
      $this->assertTrue($this->session->authenticate(["password" => $hash], $password));
      $this->assertFalse($this->session->authenticate(["password" => $hash], $badPasswords[$idx]));
    }
  }

  public function testLoginLogout() {
    // This does not login the user, it just adds them to a list of possible users.
    $user = ["id" => rand(1, 100)];
    $this->user->addUser($user);

    // Then we create a session for the user we added.
    $this->session->createSession($user, 0);

    // We should still be logged out.
    $this->assertFalse($this->session->loggedIn());

    // Then we start the session.
    $this->session->startSession();

    // Verify the user is logged in.
    $this->assertTrue($this->session->loggedIn());
    $this->assertEquals($this->storage->get("v"), $user["id"]);
    $this->assertEquals($this->user->userinfo("id"), $user["id"]);

    // Logout.
    $this->session->destroy();
    $this->assertFalse($this->session->loggedIn());
    $this->assertEmpty($this->storage->get("v"));
    $this->assertEmpty($this->user->userinfo("id"));
  }

  public function testSetAndGet() {
    $this->session->set("key", "value");
    $this->assertEquals($this->session->get("key"), "value");
  }
}
