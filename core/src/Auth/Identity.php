<?php

namespace Starbug\Core;

/**
 * Cannonical implementation of IdentityInterface.
 */
class Identity implements IdentityInterface {
  protected $user = [];
  protected $models = [];

  /**
   * Constructor.
   *
   * @param ModelFactoryInterface $models ModelFactoryInterface need to access users model.
   */
  public function __construct(ModelFactoryInterface $models) {
    $this->models = $models;
  }
  /**
   * Determine if the current user is logged in or logged in with a specific group(s).
   *
   * @param string|array $group A specific group or groups to check for.
   *
   * @return bool True if user is logged in and group conditions match. False otherwise.
   */
  public function loggedIn($group = '') {
    if (empty($this->user)) return false;
    if (empty($group)) return true;
    if (!is_array($group)) $group = [$group];
    return !empty(array_intersect($group, $this->user['groups']));
  }
  /**
   * Get a field from the current user.
   *
   * @param string $field The name of the field.
   *
   * @return mixed The value of the aforementioned field.
   */
  public function userinfo($field = '') {
    if (empty($this->user)) return false;
    return $this->user[$field] ?? null;
  }
  /**
   * Get the current user.
   *
   * @return array The current user.
   */
  public function getUser() {
    return $this->user;
  }
  /**
   * Load a user by id.
   *
   * @param integer $id The user ID.
   *
   * @return array The user record.
   */
  public function loadUser($id) {
    $query = $this->models->get("users")->query()
      ->condition("users.deleted", "0")
      ->select("GROUP_CONCAT(users.groups.slug) as groups");
    if (is_array($id)) $query->conditions($id);
    else $query->condition("users.id", $id);
    $user = $query->one();
    if (!is_array($user['groups'])) $user['groups'] = is_null($user['groups']) ? [] : explode(",", $user['groups']);
    return $user;
  }
  /**
   * Set the current user.
   *
   * @param array $user The user record as loaded by loadUser.
   *
   * @return void
   */
  public function setUser(array $user) {
    unset($user['password']);
    $this->user = $user;
  }
  /**
   * Clear the current user.
   *
   * @return void
   */
  public function clearUser() {
    $this->user = [];
  }
  /**
   * Get the hashed password.
   *
   * @param integer|array $user The user ID or user record.
   *
   * @return string The hashed password.
   */
  public function getHashedPassword($user) {
    if (!is_array($user)) $user = $this->loadUser($user);
    return $user['password'];
  }
  /**
   * Get the ID.
   *
   * @param integer|array $user The user ID or user record.
   *
   * @return integer The user ID.
   */
  public function getIdentity($user) {
    if (!is_array($user)) $user = $this->loadUser($user);
    return $user['id'];
  }
}
