<?php
namespace Starbug\Core;
/**
 * @defgroup Session
 * stateless session manager based on methodology outlined in this paper by Steven J. Murdoch
 * "Hardened Stateless Session Cookies" - http://www.cl.cam.ac.uk/~sjm217/papers/protocols08cookies.pdf
 * @ingroup lib
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
	function startSession() {
		$this->user->clearUser();
		if (false !== $this->storage->load()) {
			$user = $this->user->loadUser($this->storage->get("v"));
			if (false !== $this->validate($user)) {
				$this->user->setUser($user);
			}
		}
	}
	function loggedIn() {
		return $this->user->loggedIn();
	}
	function set($key, $value, $secure = false) {
		$this->storage->set($key, $value, $secure);
	}
	function get($key) {
		return $this->storage->get($key);
	}
	function destroy() {
		$this->user->clearUser();
		$this->storage->destroy();
	}
	function hashPassword($password) {
		//hash the password using phpass
		$hasher = new PasswordHash(8, false);
		$hash = $hasher->HashPassword($password);
		unset($hasher);

		//based on the length, separate the salt from the hash
		$lengths = array(60 => 29, 34 => 12, 20 => 9);
		$length = $lengths[strlen($hash)];
		$salt =  substr($hash, 0, $length);
		$hash = substr($hash, $length);

		//build auth token
		$token = $salt.hash('sha256', $hash);

		return $token;
	}

	/**
	* validate a password against the salt/authenticator token
	*
	* @param array/star $criteria criteria for user lookup
	* @param string $password the users password entry
	* @return bool Returns false if validation fails. If the password validates, true is returned
	*/

	function authenticate($user, $password, $duration = 0) {
		$hash = $this->user->getHashedPassword($user);
		$id = $this->user->getIdentity($user);
		if (0 == $duration) $duration = $this->duration;

		//separate salt and authenticator
		$salt = substr($hash, 0, -64);
		$auth = substr($hash, -64);

		//hash password
		if (strlen($salt) == 12) {
			$hasher = new PasswordHash(8, false);
			$hash = $hasher->crypt_private($password, $salt);
			unset($hasher);
		} else {
			$hash = crypt($password, $salt);
		}

		//separate salt and hash
		$lengths = array(60 => 29, 34 => 12, 20 => 9);
		$length = $lengths[strlen($hash)];
		$new_salt =  substr($hash, 0, $length);
		$new_hash = substr($hash, $length);

		//compare values
		if ($new_salt != $salt) return false;
		if (hash('sha256', $new_hash) != $auth) return false;

		//save data securely to session - expiry, value, hash
		$this->storage->set("e", time()+$duration, true);
		$this->storage->set("v", $id, true);
		$this->storage->set("h", $new_hash, true);

		$this->storage->save();
		return true;
	}

	/**
	* validate active session
	*/
	protected function validate($user) {
		//check expiration time
		$expiry = $this->storage->get("e");
		if (empty($expiry) || $expiry < time()) return false;

		//validate user
		$hash = $this->user->getHashedPassword($user);
		if (hash("sha256", $this->storage->get("h")) != substr($hash, -64)) return false;

		//we have a valid session
		return true;
	}
}
