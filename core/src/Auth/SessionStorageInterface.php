<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/interface/SessionStorageInterface.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Session
 */
namespace Starbug\Core;

interface SessionStorageInterface {
	/**
	 * obtain the users active session claim
	 * simply retrieves the token provided by the request
	 */
	function load();
	function set($key, $value, $secure = false);
	function get($key);
	/**
	 * store the session
	 */
	function save();
	/**
	 * destroy the active session
	 */
	function destroy();
}
