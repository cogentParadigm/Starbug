<?php
namespace Starbug\Users\Tests\Acceptance;

use DI\Attribute\Inject;
use Starbug\Db\Testing\WebDatabaseTestCase;
use Starbug\Testing\Attribute\Fixture;
use Starbug\Users\Tests\Domain\Authentication;

/**
 * Acceptance tests for authentication flows.
 *
 * Covers login, logout, session persistence, and failed-login
 * behaviour using the Authentication domain object.
 */
#[Fixture('app/fixtures/db/reset.yml')]
class AuthenticationTest extends WebDatabaseTestCase {

  #[Inject]
  protected Authentication $auth;

  /**
   * An anonymous visitor can reach the login page.
   */
  public function testLoginPageIsReachable(): void {
    $this->auth->goToLogin();

    $this->assertElementExists('input[name="email"]');
    $this->assertElementExists('input[name="password"]');
  }

  /**
   * A valid admin user can log in successfully.
   */
  public function testSuccessfulLogin(): void {
    $this->auth->login('root', 'Mz2&cssMy@9tclan');

    $this->auth->assertLoggedIn();
  }

  /**
   * An invalid password keeps the user on the login page.
   */
  public function testFailedLogin(): void {
    $this->auth->login('root', 'wrong-password');

    $this->assertPathEquals('/login');
    $this->assertPageContains('That email and password combination was not found');
    $this->auth->assertAnonymous();
  }

  /**
   * After login, session cookies persist across subsequent requests.
   */
  public function testSessionPersists(): void {
    $this->auth->login('root', 'Mz2&cssMy@9tclan');

    $this->driver->get('admin/users');
    $this->assertPathEquals('/admin/users');
  }

  /**
   * Logging out destroys the session.
   */
  public function testLogout(): void {
    $this->auth->login('root', 'Mz2&cssMy@9tclan');
    $this->auth->logout();

    $this->auth->assertAnonymous();
  }
}
