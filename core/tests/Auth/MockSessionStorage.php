<?php
namespace Starbug\Core\Tests\Auth;

use Starbug\Core\SessionStorageInterface;

/**
 * Mock implementation of SessionStorageInterface
 */
class MockSessionStorage implements SessionStorageInterface {

  public function __construct($session = false) {
    $this->session = $session;
  }

  /**
   * Create a session for the given user.
   *
   * @param integer $id The user id to create the session for.
   * @param integer $duration The session duration.
   *
   * @return void
   */
  public function createSession($id, $duration) {
    // The simplest mock is to save a single in-memory session
    // and ignore details like expiration time and token validation.
    $this->session = ["v" => $id];
  }
  /**
   * {@inheritdoc}
   *
   * @return array The session data.
   */
  public function load() {
    return $this->session;
  }
  /**
   * Set a value.
   *
   * @param string $key A property name under which to save the value.
   * @param mixed $value The value to save.
   * @param boolean $secure True if the value should be saved securely.
   *
   * @return void
   */
  public function set($key, $value, $secure = false) {
    $this->session[$key] = $value;
  }
  /**
   * Retrieve data.
   *
   * @param string $key The key/property to retrieve.
   *
   * @return mixed The value of the specified key.
   */
  public function get($key) {
    return isset($this->session[$key]) ? $this->session[$key] : null;
  }
  /**
   * {@inheritdoc}
   *
   * @return void
   */
  public function save() {
    // not needed.
  }
  /**
   * {@inheritdoc}
   *
   * @return void
   */
  public function destroy() {
    $this->session = false;
  }
}
