<?php
namespace Starbug\Core;

/**
 * Stateless session manager based on methodology outlined in this paper by Steven J. Murdoch
 * "Hardened Stateless Session Cookies" - http://www.cl.cam.ac.uk/~sjm217/papers/protocols08cookies.pdf
 */
interface SessionHandlerInterface {
  public function startSession();
  public function loggedIn();
  /**
   * Provide a salt and authenticator token for a password.
   *
   * The authenticator will be 64 characters long, with a salt prepended.
   * The salt will be 9, 12, or 29 characters long depending on the available cryptographic functions.
   *
   * @param string $password The password to hash.
   *
   * @return string The hashed password.
   */
  public function hashPassword($password);
  /**
   * Validate a password against the salt/authenticator token.
   *
   * @param array $user The user record, obtained from IdentityInterface.
   * @param string $password The users password entry.
   * @param integer $duration The valid duration of the generated session token.
   *
   * @return boolean Returns false if validation fails. If the password validates, true is returned.
   */
  public function authenticate($user, $password, $duration = 0);
  public function set($key, $value, $secure = false);
  public function get($key);
  public function destroy();
}
