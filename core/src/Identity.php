<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/Identity.php
 * @author Ali Gangji <ali@neonrain.com>
 */
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
			->select(array("groups.slug as groups", "statuses.slug as statuses"), "users");
		if (is_array($id)) $user->conditions($id);
		else $user->condition("users.id", $id);
		return $user->one();
	}
	public function setUser($user) {
		unset($user['password']);
		if (!is_array($user['groups'])) $user['groups'] = is_null($user['groups']) ? array() : explode(",", $user['groups']);
		if (!is_array($user['statuses'])) $user['statuses'] = is_null($user['statuses']) ? array() : explode(",", $user['statuses']);
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
