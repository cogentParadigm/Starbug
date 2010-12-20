<?php
class Users extends UsersModel {

	function create() {
		$user = $_POST['users'];
		return $this->store($user);
	}

	function delete() {
		return $this->remove("id=".$_POST['users']['id']);
	}

	function login() {
		$errors = array();
		$login = $_POST['users'];
		$user = $this->query("select:*  where:username='".$login['username']."' && password='".md5($login['password'])."'  limit:1");
		if (!empty($user)) {
			unset($user['password']);
			$_SESSION[P("id")] = $user['id'];
			$_SESSION[P("memberships")] = $user['memberships'];
			$_SESSION[P("user")] = $user;
		} else {
			$errors['username']['loginMatch'] = "That username and password combination was not found.";
		}
		return $errors;
	}

	function logout() {
		$_SESSION[P("id")] = 0;
		$_SESSION[P("memberships")] = 0;
		$_SESSION[P("user")] = array();
		return array();
	}
}
?>
