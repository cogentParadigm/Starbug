<?php
namespace Starbug\Users\Tests\Domain;

use Starbug\Users\Tests\Adapter\AuthAdapterInterface;
use PHPUnit\Framework\Assert;

/**
 * Domain interaction for authentication.
 *
 * Composes an AuthAdapterInterface into test-friendly operations
 * with built-in assertions.
 */
class Authentication {

  public function __construct(
    private AuthAdapterInterface $adapter
  ) {
  }

  /**
   * Log in with raw credentials.
   */
  public function login(string $email, string $password): void {
    $this->adapter->login([
      'email' => $email,
      'password' => $password
    ]);
  }

  /**
   * Log out the current session.
   */
  public function logout(): void {
    $this->adapter->logout();
  }

  /**
   * Assert the current session is authenticated.
   */
  public function assertLoggedIn(): void {
    Assert::assertTrue($this->adapter->isLoggedIn(), 'Expected user to be logged in.');
  }

  /**
   * Assert the current session is not authenticated.
   */
  public function assertAnonymous(): void {
    Assert::assertFalse($this->adapter->isLoggedIn(), 'Expected user to be anonymous.');
  }

  /**
   * Navigate to the login page.
   */
  public function goToLogin(): void {
    $this->adapter->goToLogin();
  }
}
