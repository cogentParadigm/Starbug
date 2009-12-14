<?php
class Users extends Table {

	function login() {
		$errors = array();
		$login = $_POST['users'];
		$user = $this->query("select:id, memberships	where:email='".$login['email']."' && password='".md5($login['password'])."'");
		if (count($user) == 1) {
				$_SESSION[P("id")] = $user[0]['id'];
				$_SESSION[P("memberships")] = $user[0]['memberships'];
			} else {
				$errors['email']['loginMatch'] = "That email and password combination was not found.";
			}
		return $errors;
	}

	function logout() {
		$_SESSION[P("id")] = 0;
		$_SESSION[P("memberships")] = 0;
		return array();
	}

	function create() {
		//$errors = array();
		$new_user = $_POST['users'];
		//if ($new_user['password'] != $new_user['password_confirm']) $errors['passwordConfirmError'] = true;
		//if ($new_user['email'] != $new_user['email_confirm']) $errors['emailConfirmError'] = true;
		if (!empty($new_user['password'])) $new_user['password'] = md5($new_user['password']);
		else if (!empty($new_user['id'])) unset($new_user['password']);
		return $this->store($new_user);
	}

}
?>
