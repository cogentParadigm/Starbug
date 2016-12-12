<?php
namespace Starbug\Core;
class Identity implements IdentityInterface {
	protected $user = array();
	protected $models = array();
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	public function loggedIn($group = "") {
		if (empty($this->user)) return false;
		if (empty($group)) return true;
		return in_array($group, $this->user['groups']);
	}
	public function userinfo($field = "") {
		if (empty($this->user)) return false;
		return $this->user[$field];
	}
	public function getUser() {
		return $this->user;
	}
	public function loadUser($id) {
		$user = $this->models->get("users")->query()
			->select(["groups.slug as groups"], "users");
		if (is_array($id)) $user->conditions($id);
		else $user->condition("users.id", $id);
		return $user->one();
	}
	public function setUser($user) {
		unset($user['password']);
		if (!is_array($user['groups'])) $user['groups'] = is_null($user['groups']) ? array() : explode(",", $user['groups']);
		$this->user = $user;
	}
	public function clearUser() {
		$this->user = array();
	}
	public function getHashedPassword($user) {
		if (!is_array($user)) $user = $this->loadUser($user);
		return $user['password'];
	}
	public function getIdentity($user) {
		if (!is_array($user)) $user = $this->loadUser($user);
		return $user['id'];
	}
}
