<?php
/**
 * Users model
 * @ingroup models
 */
class Users extends UsersModel {
	/**
	 * A function for an administrator to create and update users
	 */
	function create() {
		global $groups;
		$user = $_POST['users'];
		if (!in_array($user['collective'], $user['groups'])) $user['groups'][] = $user['collective'];
		$list = array_sum($user['groups']);
		$user['memberships'] = $groups['user'] + $list;
		unset($user['groups']);
		if (empty($user['id'])) $user['password'] = mt_rand(1000000,9999999);
		$errors = $this->store($user);
		if (empty($errors)) {
			if (empty($user['id'])) {
				$uid = $this->insert_id;
				$this->store("id:$uid  owner:$uid");
				$result = exec("sb email account_created $uid $user[password]");
			}
		}
	}
	/**
	 * A function for new users to register themselves
	 */
	function register() {

	}
	/**
	 * A function for current users to update their profile
	 */
	function update_profile() {
		$user = $_POST['users'];
		return $this->store($user);
	}
	/**
	 * A function to delete users
	 */
	function delete() {
		return $this->remove("id=".$_POST['users']['id']);
	}
	/**
	 * A function for logging in
	 */
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
		unset($_POST['users']['password']);
		return $errors;
	}

	/**
	 * for logging out
	 */
	function logout() {
		$_SESSION[P("id")] = 0;
		$_SESSION[P("memberships")] = 0;
		$_SESSION[P("user")] = array();
		return array();
	}

	/**
	 * resets a users password and emails it to them
	 */
	function reset_password() {
		global $sb;
		$fields = $_POST['users'];
		$email_address = trim($fields['email']);
		$errors = array();
		if (empty($email_address)) $errors['email'] = array("required" => "Please enter your email address.");
		else {
			$user = $this->query("where:email='".$email_address."'  limit:1");
			if(!empty($user)) {
				$id = $user['id'];
				$first_name = $user['first_name'];
				$last_name = $user['last_name'];
				if(empty($id)) $errors['email'][] = "Sorry, the email address you entered was not found. Please retry.";
				else {
					$new_password = mt_rand(1000000,9999999);
					$this->store("id:$id  password:$new_password");
					$result = exec("sb email password_reset $id $new_password");
					if((int)$result != 1) $errors['email'][] = "Sorry, there was a problem emailing to your address. Please retry.";
				}
			} else $errors['email'][] = "Sorry, the email address you entered was not found. Please retry.";
		}
		return $errors;
	}

}
?>
