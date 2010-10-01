<?php
	// FILE: app/plugins/UserAuthentication.php
	/**
	 * User plugin to add authentication
	 * 
	 * @package StarbugPHP
	 * @subpackage plugins
	 * @author Ali Gangji <ali@neonrain.com>
	 * @copyright 2008-2010 Ali Gangji
   */
class UserAuthentication {
	function login($users) {
		$errors = array();
		$login = $_POST['users'];
		$user = $users->query("select:id, memberships  where:username='".$login['username']."' && password='".md5($login['password'])."'  limit:1");
		if (!empty($user)) {
			$_SESSION[P("id")] = $user['id'];
			$_SESSION[P("memberships")] = $user['memberships'];
		} else {
			$errors['username']['loginMatch'] = "That username and password combination was not found.";
		}
		return $errors;
	}

	function logout($users) {
		$_SESSION[P("id")] = 0;
		$_SESSION[P("memberships")] = 0;
		return array();
	}
}
?>