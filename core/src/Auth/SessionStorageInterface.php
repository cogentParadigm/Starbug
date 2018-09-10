<?php
namespace Starbug\Core;

interface SessionStorageInterface {
  /**
   * Obtain the users active session claim.
   * Simply retrieves the token provided by the request.
   *
   * @return array The session data.
   */
  public function load();
  /**
   * Set a value.
   *
   * @param string $key A property name under which to save the value.
   * @param mixed $value The value to save.
   * @param boolean $secure True if the value should be saved securely.
   *
   * @return void
   */
  public function set($key, $value, $secure = false);
  /**
   * Retrieve data.
   *
   * @param string $key The key/property to retrieve.
   *
   * @return mixed The value of the specified key.
   */
  public function get($key);
  /**
   * Store the session.
   *
   * @return void
   */
  public function save();
  /**
   * Destroy the active session.
   *
   * @return void
   */
  public function destroy();
}
