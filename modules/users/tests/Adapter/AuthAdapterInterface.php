<?php
namespace Starbug\Users\Tests\Adapter;

/**
 * Contract for authentication protocol adapters.
 *
 * Implementations translate login/logout operations into
 * transport-specific calls (HTTP, browser, shell, etc.).
 */
interface AuthAdapterInterface {

  /**
   * Log in with the provided credentials.
   *
   * @param array $credentials Typically ['email' => ..., 'password' => ...].
   */
  public function login(array $credentials): void;

  /**
   * Log out the current session.
   */
  public function logout(): void;

  /**
   * Check if the current session is authenticated.
   *
   * @return bool True if authenticated, false otherwise.
   */
  public function isLoggedIn(): bool;

  /**
   * Navigate to the login page.
   */
  public function goToLogin(): void;
}
