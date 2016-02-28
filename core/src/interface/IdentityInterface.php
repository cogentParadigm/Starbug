<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/IdentityInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
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
