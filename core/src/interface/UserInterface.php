<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/UserInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 */
namespace Starbug\Core;
interface UserInterface {
	public function startSession();
	public function loggedIn($group = "");
	public function userinfo($field = "");
	public function getUser();
	public function setUser($user);
	public function clearUser();
}
