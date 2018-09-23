<?php
namespace Starbug\Core;

class SessionHandler implements SessionHandlerInterface {
  protected $user;
  protected $storage;
  protected $duration;

  public function __construct(SessionStorageInterface $storage, IdentityInterface $user, $duration = 2592000) {
    $this->storage = $storage;
    $this->user = $user;
    $this->duration = $duration;
  }
  /**
   * Start the session. Called early to see if there's an active session and load it.
   *
   * @return void
   */
  public function startSession() {
    $this->user->clearUser();
    if (false !== $this->storage->load() && $user = $this->user->loadUser($this->storage->get("v"))) {
      $this->user->setUser($user);
    }
  }
  /**
   * Create a session for the given user.
   *
   * @param array $user The user to create the session for. This should have come from the IdentityInterface.
   * @param integer $duration The session duration. Leave off to use the default duration.
   *
   * @return void
   */
  public function createSession($user, $duration = 0) {
    $id = $this->user->getIdentity($user);
    if (0 == $duration) $duration = $this->duration;
    $this->storage->createSession($id, $duration);
  }

  /**
   * Hash a password.
   *
   * @param string $password The plain text password to hash.
   *
   * @return string The hashed password.
   */
  public function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
  }

  /**
   * Validate a password against the saved hash.
   *
   * @param array $user The user record, obtained from IdentityInterface.
   * @param string $password The users password entry.
   *
   * @return boolean Returns false if validation fails. If the password validates, true is returned.
   */
  public function authenticate($user, $password) {
    $hash = $this->user->getHashedPassword($user);
    return password_verify($password, $hash);
  }

  /**
   * Destroy the session.
   *
   * @return void
   */
  public function destroy() {
    $this->user->clearUser();
    $this->storage->destroy();
  }

  /**
   * Check if a user is logged in. Session must be started first.
   *
   * @return void
   */
  public function loggedIn() {
    return $this->user->loggedIn();
  }

  /**
   * Save a value in the client session.
   *
   * @param string $key A lookup name for the value.
   * @param string $value The value.
   * @param boolean $secure True to store encrypted.
   *
   * @return void
   */
  public function set($key, $value, $secure = false) {
    $this->storage->set($key, $value, $secure);
  }

  /**
   * Get a value stored in the client session.
   *
   * @param string $key The property name.
   *
   * @return void
   */
  public function get($key) {
    return $this->storage->get($key);
  }
}
