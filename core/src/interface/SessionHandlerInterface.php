<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Session.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Session
 */
namespace Starbug\Core;
/**
 * @defgroup Session
 * stateless session manager based on methodology outlined in this paper by Steven J. Murdoch
 * "Hardened Stateless Session Cookies" - http://www.cl.cam.ac.uk/~sjm217/papers/protocols08cookies.pdf
 * @ingroup lib
 */
interface SessionHandlerInterface {
	function startSession();
	function loggedIn();
	/**
	* provide a salt and authenticator token for a password
	*
	* The authenticator will be 64 characters long, with a salt prepended.
	* The salt will be 9, 12, or 29 characters long depending on the available cryptographic functions.
	*
	* @param string $password
	* @return string $token
	*/
	function hashPassword($password);
	/**
	* validate a password against the salt/authenticator token
	*
	* @param string $id a token identifyng the user to authenticate
	* @param string $password the users password entry
	* @param int $duration in seconds. 0 should be converted to a configured default length
	* @return bool Returns false if validation fails. If the password validates, true is returned
	*/
	function authenticate($id, $password, $duration = 0);
	function set($key, $value, $secure = false);
	function get($key);
	function destroy();
}
