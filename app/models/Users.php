<?php
class Users extends Table {

	function login() {
		$errors = array();
		$login = $_POST['users'];
		$user = $this->get("id, security", "email='".$login['email']."' && password='".md5($login['password'])."'");
		if (!$user->EOF) {
				$_SESSION[P("id")] = $user->fields['id'];
				$_SESSION[P("security")] = $user->fields['security'];
				$_POST['users']['first_name'] = $user->fields['first_name'];
			} else {
				$errors['loginMatchError'] = true;
			}
		return $errors;
	}

	function logout() {
		$_SESSION[P("id")] = null;
		$_SESSION[P("security")] = null;
		return array();
	}

	function create() {
		if ($_SESSION[P('security')] != Etc::SUPER_ADMIN_SECURITY) return array('securityError');
		//$errors = array();
		$new_user = $_POST['users'];
		//if ($new_user['password'] != $new_user['password_confirm']) $errors['passwordConfirmError'] = true;
		//if ($new_user['email'] != $new_user['email_confirm']) $errors['emailConfirmError'] = true;
		if (!empty($new_user['password'])) $new_user['password'] = md5($new_user['password']);
		return $this->store($new_user);
	}

}
?>