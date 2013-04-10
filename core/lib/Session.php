<?php
# Copyright (C) 2008-2010 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/lib/Session.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Session
 */
$this->import("core/lib/PasswordHash");
$this->provide("core/lib/Session");
/**
 * @defgroup Session
 * stateless session manager based on methodology outlined in this paper by Steven J. Murdoch
 * "Hardened Stateless Session Cookies" - http://www.cl.cam.ac.uk/~sjm217/papers/protocols08cookies.pdf
 * @ingroup lib
 */
class Session {
    
	/**
	 * provide a salt and authenticator token for a password
	 * 
	 * The authenticator will be 64 characters long, with a salt prepended.
	 * The salt will be 9, 12, or 29 characters long depending on the available cryptographic functions.
	 * 
	 * @param string $password 
	 * @return string $token
	 */
	function hash_password($password) {	
		//hash the password using phpass
		$hasher = new PasswordHash(8, FALSE);
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
	
	function authenticate($hash, $password, $data, $key, $duration=86400) {
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

		//generate cookie containing expiry, value, hash, and digest
		$session = "e=".(time()+$duration)."&v=".$data."&h=".urlencode($new_hash);
		//append digest
		$session .= '&d='.urlencode(hash_hmac("sha256", $session, $key));
		
		//save cookie and return
		if (!defined("SB_CLI")) setcookie("sid", $session, 0, uri(), null, false, true);
		return true;
	}
	
	function active() {
		//obtain and parse session cookie
		$session = $_COOKIE['sid'];
		if (empty($session)) return false;
		parse_str($session, $params);
		return $params;
	}

	/**
	 * validate active session
	 */
	function validate($session, $hash, $key) {
		//check expiration time
		if(empty($session['e']) || $session['e'] < time()) return false;

		//verify cookie integrity
		if (hash_hmac("sha256", "e=".$session['e']."&v=".$session['v']."&h=".urlencode($session['h']), $key) != $session['d']) return false;

		//validate user
		if (hash("sha256", $session['h']) != substr($hash, -64)) return false;
		
		//we have a valid session
		return true;
	}
	
	function destroy() {
		if (!defined("SB_CLI")) setcookie("sid", null, time(), uri(), null, false, true);
	}

}
