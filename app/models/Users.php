<?php
class Users extends Table {

	function login() {
		$errors = array();
		$login = $_POST['user'];
		$user = $this->get("id, security", "email='".$login['email']."' && password='".md5($login['password'])."'");
		if (!$user->EOF) {
				$_SESSION[P("id")] = $user->fields['id'];
				$_SESSION[P("security")] = $user->fields['security'];
				$_POST['user']['first_name'] = $user->fields['first_name'];
			} else {
				$errors['loginMatchError'] = true;
			}
		return $errors;
	}

	function logout() {
		$_SESSION[P("id")] = null;
		$_SESSION[P("security")] = null;
		$currentPageSecurity = $this->get_object("Actions")->get("security", Etc::ACTION_COLUMN."='".$_GET["action"]."'");
		if(!$currentPageSecurity->EOF) $currentPageSecurity = $currentPageSecurity->fields['security'];
		if($currentPageSecurity > 1) $_GET["action"] = "Home";
		return array();
	}

	function create() {
		$errors = array();
		$new_user = $_POST['user'];
		if ($new_user['password'] != $new_user['password_confirm']) $errors['passwordConfirmError'] = true;
		if ($new_user['email'] != $new_user['email_confirm']) $errors['emailConfirmError'] = true;
		$new_user['password'] = md5($new_user['password']);
		return arraymerge($this-store($new_user), $errors);
	}

}
?>