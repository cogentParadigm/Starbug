<?php
# Copyright (C) 2016 Ali Gangji
# Distributed under the terms of the GNU General Public License v3
/**
 * This file is part of StarbugPHP
 * @file core/src/SessionStorage.php
 * @author Ali Gangji <ali@neonrain.com>
 * @ingroup Session
 */
namespace Starbug\Core;
/**
 * Cookie based implementation of SessionStorageInterface
 */
class SessionStorage implements SessionStorageInterface {

	private $request;
	private $key;
	private $data = array();
	private $secure = array();

	public function __construct(RequestInterface $request, $key) {
		$this->request = $request;
		$this->key = $key;
	}
	/**
	 * obtain the users active session claim
	 * simply retrieves the token provided by the request
	 */
	function load() {
		//obtain and parse session cookie
		$session = $this->request->getCookie("sid");
		if (empty($session)) return false;
		parse_str($session, $params);
		$digest = $params['d'];
		unset($params['d']);
		//validate cookie integrity
		if (hash_hmac("sha256", http_build_query($params), $this->key) != $digest) return false;
		$this->data = $params;
		return $this->data;
	}
	function set($key, $value, $secure = false) {
		$this->data[$key] = $value;
		$this->secure[$key] = $secure;
	}
	function get($key) {
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}
	/**
	 * store the session
	 */
	function save() {
		//encode session data
		$session = http_build_query($this->data);
		//append digest
		$session .= '&d='.urlencode(hash_hmac("sha256", $session, $this->key));
		//write cookies
		$url = $this->request->getURL();
		$oid = md5(uniqid(mt_rand(), true));
		setcookie("sid", $session, $this->data['e'], $url->build(""), null, false, true);
		setcookie("oid", $oid, $this->data['e'], $url->build(""), null, false, false);
		$this->request->setCookie("sid", $session);
		$this->request->SetCookie("oid", $oid);
	}
	/**
	 * destroy the active session
	 */
	function destroy() {
		$this->data = array();
		$url = $this->request->getURL();
		setcookie("sid", null, time(), $url->build(""), null, false, true);
	}
}
