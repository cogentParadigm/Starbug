<?php
class Users extends Table {
	
	var $filters = array(
		"email" => "unique:",
		"password" => "confirm:password_confirm  md5:  optional_update:"
	);

	function login() {
		$errors = array();
		$login = $_POST['users'];
		$user = $this->query("select:id, memberships  where:username='".$login['username']."' && password='".md5($login['password'])."'  limit:1");
		if (!empty($user)) {
				$_SESSION[P("id")] = $user[0]['id'];
				$_SESSION[P("memberships")] = $user[0]['memberships'];
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

	function create() {
		$user = $_POST['users'];
		return $this->store($new_user);
	}

}
?>
