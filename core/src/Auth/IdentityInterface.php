<?php
namespace Starbug\Core;
interface IdentityInterface {
	public function loggedIn($group = "");
	public function userinfo($field = "");
	public function getUser();
	public function loadUser($id);
	public function setUser($user);
	public function clearUser();
	public function getHashedPassword($user);
	public function getIdentity($user);
}
