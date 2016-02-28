<?php
/**
 * Users model
 * @ingroup models
 */
namespace Starbug\Users;
use Starbug\Core\UsersModel;
use Starbug\Core\Session;
use \Etc;
class Users extends UsersModel {

	/**
	 * A function for an administrator to create and update users
	 */
	function create($user) {
		if ($this->user->loggedIn("root") || $this->user->loggedIn("admin")) {
			foreach ($user as $k => $v) if (empty($v) && $k != "email") unset($user[$k]);
		}
		$this->store($user);
		if ((!$this->errors()) && (empty($user['id']))) {
			$uid = $this->insert_id;
			$data = array("user" => $this->load($uid));
			$data['user']['password'] = $user['password'];
			$this->mailer->send(array("template" => "Account Creation", "to" => $user['email']), $data);
		}
	}

	function delete($user) {
		$this->store(array("id" => $user['id'], "statuses" => "deleted"));
	}

	/**
	 * A function for new users to register themselves
	 */
	function register($user, $redirect=true) {
		$this->store(array("email" => $user['email'], "password" => $user['password'], "password_confirm" => $user['password_confirm'], "groups" => "user"));
		if (!$this->errors()) {
			$this->login(array("email" => $user['email'], "password" => $user['password']), $redirect);
		}
	}

	/**
	 * A function for current users to update their profile
	 */
	function update_profile($profile) {
		//force the user to enter their current password to update their profile
		//validate it by authenticating the sessiong against the entry
		if ($this->session->authenticate($profile['id'], $profile['current_password'])) {
			$this->store(array("id" => $profile['id'], "email" => $profile['email'], "password" => $profile['password'], "password_confirm" => $profile['password_confirm']));
			if (!$this->errors() && !empty($profile['password'])) {
				//changing your password will invalidate your current session
				//so we need to re-authenticate using the new password
				$this->session->authenticate($profile['id'], $profile['password']);
			}
		} else {
			$this->error("Your credentials could not be authenticated.", "current_password");
		}
	}

	/**
	 * A function for logging in
	 */
	function login($login, $redirect=true) {
		$user = $this->user->loadUser(array("email" => $login['email']));
		if ($this->session->authenticate($user, $login['password'])) {
			$this->user->setUser($user);
			$this->store(array("id" => $user['id'], "last_visit" => date("Y-m-d H:i:s")));
		} else {
			$this->error("That email and password combination was not found.", "email");
		}
	}

	/**
	 * for logging out
	 */
	function logout() {
		$this->session->destroy();
		return array();
	}

	/**
	 * resets a users password and emails it to them
	 */
	function reset_password($fields) {
		$email_address = trim($fields['email']);
		if (empty($email_address)) $this->error("Please enter your email address.", "email");
		else {
			$user = $this->query()->condition("email", $email_address)->one();
			if (!empty($user)) {
				$id = $user['id'];
				if (empty($id)) $this->error("Sorry, the email address you entered was not found. Please retry.", "email");
				else {
					$new_password = mt_rand(1000000, 9999999);
					$this->store("id:$id  password:$new_password");
					$data = array("user" => $user);
					$data['user']['password'] = $new_password;
					$result = $this->mailer->send(array("template" => "Password Reset", "to" => $user['email']), $data);
					if ((int)$result != 1) $this->error("Sorry, there was a problem emailing to your address. Please retry.", "email");
				}
			} else $this->error("Sorry, the email address you entered was not found. Please retry.", "email");
		}
	}

	function query_admin($query, &$ops) {
		$query->select("users.*");
		$query->select("users.groups.id as groups");
		$query->select("users.statuses.id as statuses");
		if (!empty($ops['group']) && is_numeric($ops['group'])) {
			$query->condition("users.groups.id", $ops['group']);
		}

		if (!empty($ops['status']) && is_numeric($ops['status'])) $query->condition("users.statuses.id", $ops['status']);
		else $query->condition("users.statuses.slug", "deleted", "!=", array("ornull" => true));
		return $query;
	}

	function filter($row) {
		//even though it shouldn't be useful to attackers,
		//we don't want the password hash to be returned in api calls
		unset($row['password']);
		return $row;
	}
}
?>
