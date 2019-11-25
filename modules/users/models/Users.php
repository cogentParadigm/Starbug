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
   * E-mails user a link to reset their password
   */
  public function forgotPassword($fields) {
    $email = trim($fields['email']);
    if (empty($email)) {
      $this->error("Please enter your email address.", "email");
      return;
    }
    $user = $this->query()->condition("email", $email)->one();
    if (empty($user)) {
      $this->error("Sorry, the email address you entered was not found. Please retry.", "email");
      return;
    }
    $id = $user['id'];
    $token = bin2hex(openssl_random_pseudo_bytes(16));
    $data = ["user" => $user];
    $this->store(["id" => $id, "password_token" => $token]);
    $data['user']['password-reset-link'] = $this->url->build('reset-password?token='.$token, true);
    $result = $this->mailer->send(["template" => "Forgot Password", "to" => $user['email']], $data);
    if ($result != 1) $this->error("Sorry, there was a problem emailing to your address. Please retry.", "email");
  }

  /**
   * Resets a users password
   */
  public function resetPassword($data) {
    $user = $this->db->query("users")->condition("email", $data['email'])->one();
    if (empty($user['password_token']) || $user['password_token'] != $data['token']) {
      $this->error("Your password reset request could not be verified. Please follow the link you were emailed.", "email");
      return;
    }
    if (empty($data["password"])) {
      $this->error("This field is required", "password");
      return;
    }
    $this->store([
      "id" => $user['id'],
      "password" => $data['password'],
      "password_confirm" => $data["password_confirm"],
      "password_token" => ""
    ]);
  }
}
