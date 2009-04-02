<?php
class Users extends Table {

	function login() {
		$errors = array();
		$login = $_POST['users'];
		$user = $this->get("id, memberships", "email='".$login['email']."' && password='".md5($login['password'])."'");
		if (!$user->EOF) {
				$_SESSION[P("id")] = $user->fields['id'];
				$_SESSION[P("memberships")] = $user->fields['memberships'];
			} else {
				$errors['loginMatchError'] = true;
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
