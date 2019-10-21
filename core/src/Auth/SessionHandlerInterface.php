<?php
namespace Starbug\Core;

interface SessionHandlerInterface {
  /**
   * Start the session. Called early to see if there's an active session and load it.
   *
   * @return void
   */
  public function startSession();
  /**
   * Create a session for the given user.
   *
   * @param array $user The user to create the session for. This should have come from the IdentityInterface.
   * @param integer $duration The session duration. Leave off to use the default duration.
   *
   * @return void
   */
  public function createSession($user, $duration = 0);
  /**
   * Hash a password.
   *
   * @param string $password The plain text password to hash.
   *
   * @return string The hashed password.
   */
  public function hashPassword($password);
  /**
   * Validate a password against the saved hash.
   *
   * @param array $user The user record, obtained from IdentityInterface.
   * @param string $password The users password entry.
   *
   * @return boolean Returns false if validation fails. If the password validates, true is returned.
   */
  public function authenticate($user, $password);
  /**
   * Destroy the session.
   *
   * @return void
   */
  public function destroy();
  /**
   * Check if a user is logged in. Session must be started first.
   *
   * @return void
   */
  public function loggedIn();
  /**
   * Save a value in the client session.
   *
   * @param string $key A lookup name for the value.
   * @param string $value The value.
   * @param boolean $secure True to store encrypted.
   *
   * @return void
   */
  public function set($key, $value, $secure = false);
  /**
   * Get a value stored in the client session.
   *
   * @param string $key The property name.
   *
   * @return void
   */
  public function get($key);
}
