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
		$user = $this->query("select:id, memberships  where:username='".$login['username']."' && password='".md5($login['password'])."'  limit:1");
		if (!empty($user)) {
			$_SESSION[P("id")] = $user['id'];
			$_SESSION[P("memberships")] = $user['memberships'];
		} else {
			$errors['username']['loginMatch'] = "That username and password combination was not found.";
		}
		return $errors;
	}

	function logout() {
		$_SESSION[P("id")] = 0;
		$_SESSION[P("memberships")] = 0;
		return array();
	}
}
?>
