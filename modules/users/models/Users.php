<?php
namespace Starbug\Users;

use Starbug\Core\UsersModel;

class Users extends UsersModel {

  /**
   * A function for an administrator to create and update users
   */
  public function create($user) {
    $this->store($user);
    if ((!$this->errors()) && (empty($user['id']))) {
      $uid = $this->insert_id;
      $data = ["user" => $this->load($uid)];
      $data['user']['password'] = $user['password'];
      $this->mailer->send(["template" => "Account Creation", "to" => $user['email']], $data);
    }
  }

  public function delete($user) {
    $this->store(["id" => $user['id'], "deleted" => "1"]);
  }

  /**
   * A function for new users to register themselves
   */
  public function register($user) {
    $this->store(["email" => $user['email'], "password" => $user['password'], "password_confirm" => $user['password_confirm'], "groups" => "user"]);
    if (!$this->errors()) {
      $this->login(["email" => $user['email'], "password" => $user['password']]);
    }
  }

  /**
   * A function for current users to update their profile
   */
  public function updateProfile($profile) {
    // force the user to enter their current password to update their profile
    // validate it by authenticating the sessiong against the entry
    if ($this->session->authenticate($profile['id'], $profile['current_password'])) {
      $this->store(["id" => $profile['id'], "email" => $profile['email'], "password" => $profile['password'], "password_confirm" => $profile['password_confirm']]);
    } else {
      $this->error("Your credentials could not be authenticated.", "current_password");
    }
  }

  /**
   * A function for logging in
   */
  public function login($login) {
    $user = $this->user->loadUser(["email" => $login['email']]);
    if ($this->session->authenticate($user, $login['password'])) {
      $this->session->createSession($user);
      $this->user->setUser($user);
      $this->store(["id" => $user['id'], "last_visit" => date("Y-m-d H:i:s")]);
    } else {
      $this->error("That email and password combination was not found.", "email");
    }
  }

  /**
   * For logging out
   */
  public function logout() {
    $this->session->destroy();
    return [];
  }

  /**
   * Resets a users password and emails it to them
   */
  public function resetPassword($fields) {
    $email_address = trim($fields['email']);
    if (empty($email_address)) $this->error("Please enter your email address.", "email");
    else {
      $user = $this->query()->condition("email", $email_address)->one();
      if (!empty($user)) {
        $id = $user['id'];
        if (empty($id)) $this->error("Sorry, the email address you entered was not found. Please retry.", "email");
        else {
          $new_password = mt_rand(1000000, 9999999);
          $this->store(["id" => $id, "password" => $new_password]);
          $data = ["user" => $user];
          $data['user']['password'] = $new_password;
          $result = $this->mailer->send(["template" => "Password Reset", "to" => $user['email']], $data);
          if ((int) $result != 1) $this->error("Sorry, there was a problem emailing to your address. Please retry.", "email");
        }
      } else $this->error("Sorry, the email address you entered was not found. Please retry.", "email");
    }
  }
}
