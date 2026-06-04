<?php
namespace Starbug\Users\Tests\Adapter;

use Starbug\Testing\WebAdapter;

/**
 * HTTP-based protocol adapter for authentication actions.
 *
 * Translates login/logout operations into HTTP requests
 * via the underlying driver.
 */
class AuthWebAdapter extends WebAdapter implements AuthAdapterInterface {

  public function login(array $credentials): void {
    $this->driver->submitForm('/login', $credentials);
  }

  public function logout(): void {
    $this->visit('/logout');
  }

  /**
   * Check if the current session is authenticated.
   *
   * Makes a request to a protected route and returns true
   * if not redirected to the login page.
   */
  public function isLoggedIn(): bool {
    $this->visit('/admin');
    return $this->driver->getCurrentPath() !== '/login';
  }

  /**
   * Navigate to the login page.
   */
  public function goToLogin(): void {
    $this->visit('/login');
  }
}
