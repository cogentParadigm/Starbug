<?php
/**
 * Users model
 * @ingroup models
 */
class Users {
	
	var $statuses = array(
		"disabled" => 1,
		"enabled" => 4
	);
	
	/**
	 * A function for an administrator to create and update users
	 */
	function create($user) {
		if (logged_in("root") || logged_in("admin")) {
			foreach ($user as $k => $v) if (empty($v) && $k != "email") unset($user[$k]);
		}
		if (!empty($user['groups'])) {
			$user['memberships'] = array_sum($user['groups']);
			unset($user['groups']);
		}
		$this->store($user);
		if ((!errors()) && (empty($user['id']))) {
			$uid = $this->insert_id;
			$result = exec("sb email account_created $uid $user[password]");
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
	function update_profile($user) {
		return $this->store($user);
	}

	/**
	 * A function for logging in
	 */
	function login($login) {
		$user = $this->query("select:*  where:email=?  limit:1", array($login['username']));
		if (Session::authenticate($user['password'], $login['password'], $user['id'], Etc::HMAC_KEY)) {
			sb()->user = $user;
			unset($user['password']);
			$this->store(array("id" => $user['id'], "last_visit" => date("Y-m-d H:i:s")));
			if (logged_in('admin') || logged_in('root')) redirect(uri('admin'));
		} else {
			error("That username and password combination was not found.", "username");
		}
		unset($_POST['users']['password']);
	}

	/**
	 * for logging out
	 */
	function logout() {
		Session::destroy();
		return array();
	}

	/**
	 * resets a users password and emails it to them
	 */
	function reset_password($fields) {
		global $sb;
		$email_address = trim($fields['email']);
		if (empty($email_address)) error("Please enter your email address.", "email");
		else {
			$user = $this->query("where:email='".$email_address."'  limit:1");
			if(!empty($user)) {
				$id = $user['id'];
				$first_name = $user['first_name'];
				$last_name = $user['last_name'];
				if(empty($id)) error("Sorry, the email address you entered was not found. Please retry.", "email");
				else {
					$new_password = mt_rand(1000000,9999999);
					$this->store("id:$id  password:$new_password");
					$result = exec("sb email password_reset $id $new_password");
					if((int)$result != 1) error("Sorry, there was a problem emailing to your address. Please retry.", "email");
				}
			} else error("Sorry, the email address you entered was not found. Please retry.", "email");
		}
	}
	
	function query_admin($query) {
		$query['params'] = array();
		if (!empty($query['group']) && is_numeric($query['group'])) {
			$query['where'][] = 'memberships & ?';
			$query['params'][] = $query['group'];
		}
		
		if (!empty($query['status']) && is_numeric($query['status'])) $query['where'][] = "(users.status & ".$query['status'].")";
		else $query['where'][] = "!(users.status & 1)";
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
