<?php

namespace spec\Starbug\Core;

use Starbug\Core\SessionHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Starbug\Core\SessionStorageInterface;
use Starbug\Core\IdentityInterface;

/**
 * Spec test for Starbug\Core\SessionHandler.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
class SessionHandlerSpec extends ObjectBehavior {
  public function let(SessionStorageInterface $storage, IdentityInterface $user) {
    $this->beConstructedWith($storage, $user);
  }
  public function it_is_initializable() {
    $this->shouldHaveType(SessionHandler::class);
  }
  public function it_starts_an_empty_session($storage, $user) {
    // Given.
    $storage->load()->willReturn(false);

    // Then.
    $user->clearUser()->shouldBeCalled();
    $storage->load()->shouldBeCalled();

    // When.
    $this->startSession();
  }
  public function it_starts_an_active_session($storage, $user) {
    // Given.
    $storage->load()->willReturn(true);
    $storage->get("v")->willReturn(10);
    $user->loadUser(10)->willReturn(["id" => 10]);

    // Then.
    $user->clearUser()->shouldBeCalled();
    $storage->load()->shouldBeCalled();
    $storage->get("v")->shouldBeCalled();
    $user->loadUser(10)->shouldBeCalled();
    $user->setUser(["id" => 10])->shouldBeCalled();

    // When.
    $this->startSession();
  }
  public function it_creates_a_session($storage, $user) {
    // Given.
    $this->beConstructedWith($storage, $user, 100);
    $user->getIdentity(["id" => 10])->willReturn(10);

    // Then.
    $user->getIdentity(["id" => 10])->shouldBeCalled();
    $storage->createSession(10, 100)->shouldBeCalled();

    // When.
    $this->createSession(["id" => 10]);
  }
  public function it_creates_a_session_with_a_custom_duration($storage, $user) {
    // Given.
    $this->beConstructedWith($storage, $user, 100);
    $user->getIdentity(["id" => 10])->willReturn(10);

    // Then.
    $user->getIdentity(["id" => 10])->shouldBeCalled();
    $storage->createSession(10, 50)->shouldBeCalled();

    // When.
    $this->createSession(["id" => 10], 50);
  }
  public function it_hashes_and_validates_passwords($user) {
    // Given.
    $password = "mysupersecretpassword";
    $notmypassword = "notmysupersecretpassword";
    $hashed = $this->hashPassword($password);
    $user->getHashedPassword(["password" => $hashed])->willReturn($hashed);

    // Then.
    $this->authenticate(["password" => $hashed], $password)
      ->shouldBe(true);

    // And.
    $this->authenticate(["password" => $hashed], $notmypassword)
      ->shouldBe(false);
  }
  public function it_destroys_the_session($storage, $user) {
    // Then.
    $user->clearUser()->shouldBeCalled();
    $storage->destroy()->shouldBeCalled();

    // When.
    $this->destroy();
  }
  public function it_tells_us_if_a_user_is_logged_in($user) {
    // Given.
    $user->loggedIn()->willReturn(true);

    // Then.
    $this->loggedIn()->shouldBe(true);

    // Given.
    $user->loggedIn()->willReturn(false);

    // Then.
    $this->loggedIn()->shouldBe(false);
  }
  public function it_sets_values_in_the_session_storage($storage) {
    // Then.
    $storage->set("key", "value", true)->shouldBeCalled();

    // When.
    $this->set("key", "value", true);
  }
  public function it_gets_values_from_the_session_storage($storage) {
    // Given.
    $storage->get("key")->willReturn("value");

    // Then.
    $this->get("key")->shouldBe("value");
  }
}
