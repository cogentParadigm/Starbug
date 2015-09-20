<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/User.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
class User implements UserInterface {
	protected $user = array();
	protected $db = array();
	public function __construct(DatabaseInterface $db) {
		$this->db = $db;
	}
	public function startSession() {
		$this->clearUser();
		if (false !== ($session = Session::active())) {
			if (!empty($session['v']) && is_numeric($session['v'])) {
				$user = $this->db->query("users");
				$user = $user->select("users.*,users.groups as groups,users.statuses as statuses")->condition("users.id", $session['v'])->one();
				if (Session::validate($session, $user['password'], \Etc::HMAC_KEY)) {
					$this->setUser($user);
				}
			}
		}
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
	public function setUser($user) {
		unset($user['password']);
		if (!is_array($user['groups'])) $user['groups'] = is_null($user['groups']) ? array() : explode(",", $user['groups']);
		if (!is_array($user['statuses'])) $user['statuses'] = is_null($user['statuses']) ? array() : explode(",", $user['statuses']);
		$this->user = $user;
	}
	public function clearUser() {
		$this->user = array();
	}
}
