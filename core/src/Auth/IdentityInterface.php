<?php

namespace Starbug\Core;

interface IdentityInterface {
  /**
   * Determine if the current user is logged in or logged in with a specific group(s).
   *
   * @param string|array $group A specific group or groups to check for.
   *
   * @return bool True if user is logged in and group conditions match. False otherwise.
   */
  public function loggedIn($group = "");
  /**
   * Get a field from the current user.
   *
   * @param string $field The name of the field.
   *
   * @return mixed The value of the aforementioned field.
   */
  public function userinfo($field = "");
  /**
   * Get the current user.
   *
   * @return array The current user.
   */
  public function getUser();
  /**
   * Load a user by id.
   *
   * @param integer $id The user ID.
   *
   * @return array The user record.
   */
  public function loadUser($id);
  /**
   * Set the current user.
   *
   * @param array $user The user record as loaded by loadUser.
   *
   * @return void
   */
  public function setUser(array $user);
  /**
   * Clear the current user.
   *
   * @return void
   */
  public function clearUser();
  /**
   * Get the hashed password.
   *
   * @param integer|array $user The user ID or user record.
   *
   * @return string The hashed password.
   */
  public function getHashedPassword($user);
  /**
   * Get the ID.
   *
   * @param integer|array $user The user ID or user record.
   *
   * @return integer The user ID.
   */
  public function getIdentity($user);
}
