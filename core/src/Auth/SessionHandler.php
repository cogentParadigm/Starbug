<?php
namespace Starbug\Core;

/**
 * Stateless session manager based on methodology outlined in this paper by Steven J. Murdoch
 * "Hardened Stateless Session Cookies" - http://www.cl.cam.ac.uk/~sjm217/papers/protocols08cookies.pdf
 */
class SessionHandler implements SessionHandlerInterface {
  protected $user;
  protected $storage;
  protected $duration;

  public function __construct(SessionStorageInterface $storage, IdentityInterface $user, $duration = 2592000) {
    $this->storage = $storage;
    $this->user = $user;
    $this->duration = $duration;
  }
  public function startSession() {
    $this->user->clearUser();
    if (false !== $this->storage->load()) {
      $user = $this->user->loadUser($this->storage->get("v"));
      if (false !== $this->validate($user)) {
        $this->user->setUser($user);
      }
    }
  }
  public function loggedIn() {
    return $this->user->loggedIn();
  }
  public function set($key, $value, $secure = false) {
    $this->storage->set($key, $value, $secure);
  }
  public function get($key) {
    return $this->storage->get($key);
  }
  public function destroy() {
    $this->user->clearUser();
    $this->storage->destroy();
  }
  public function hashPassword($password) {
    // Hash the password using phpass.
    $hasher = new PasswordHash(8, false);
    $hash = $hasher->HashPassword($password);
    unset($hasher);

    // Based on the length, separate the salt from the hash.
    $lengths = [60 => 29, 34 => 12, 20 => 9];
    $length = $lengths[strlen($hash)];
    $salt =  substr($hash, 0, $length);
    $hash = substr($hash, $length);

    // Build auth token.
    $token = $salt.hash('sha256', $hash);

    return $token;
  }

  /**
   * Validate a password against the salt/authenticator token.
   *
   * @param array $user The user record, obtained from IdentityInterface.
   * @param string $password The users password entry.
   * @param integer $duration The valid duration of the generated session token.
   *
   * @return boolean Returns false if validation fails. If the password validates, true is returned.
   */
  public function authenticate($user, $password, $duration = 0) {
    $hash = $this->user->getHashedPassword($user);
    $id = $this->user->getIdentity($user);
    if (0 == $duration) $duration = $this->duration;

    // Separate salt and authenticator.
    $salt = substr($hash, 0, -64);
    $auth = substr($hash, -64);

    // Hash password.
    if (strlen($salt) == 12) {
      $hasher = new PasswordHash(8, false);
      $hash = $hasher->crypt_private($password, $salt);
      unset($hasher);
    } else {
      $hash = crypt($password, $salt);
    }

    // Separate salt and hash.
    $lengths = [60 => 29, 34 => 12, 20 => 9];
    $length = $lengths[strlen($hash)];
    $new_salt =  substr($hash, 0, $length);
    $new_hash = substr($hash, $length);

    // Compare values.
    if ($new_salt != $salt) return false;
    if (hash('sha256', $new_hash) != $auth) return false;

    // Save data securely to session - expiry, value, hash.
    $this->storage->set("e", time()+$duration, true);
    $this->storage->set("v", $id, true);
    $this->storage->set("h", $new_hash, true);

    $this->storage->save();
    return true;
  }

  /**
   * Validate active session.
   */
  protected function validate($user) {
    // Check expiration time.
    $expiry = $this->storage->get("e");
    if (empty($expiry) || $expiry < time()) return false;

    // Validate user.
    $hash = $this->user->getHashedPassword($user);
    if (hash("sha256", $this->storage->get("h")) != substr($hash, -64)) return false;

    // We have a valid session.
    return true;
  }
}
