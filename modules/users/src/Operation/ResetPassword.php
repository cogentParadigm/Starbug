<?php
namespace Starbug\Users\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;

class ResetPassword extends Save {
  public function handle(array $data, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    $user = $this->db->query("users")->condition("email", $data['email'])->one();
    if (empty($user['password_token']) || $user['password_token'] != $data['token']) {
      $this->db->error("Your password reset request could not be verified. Please follow the link you were emailed.", "email", "users");
    }
    if (empty($data["password"])) {
      $this->db->error("This field is required", "password", "users");
    }
    if (!$this->db->errors()) {
      $this->db->store("users", [
        "id" => $user['id'],
        "password" => $data['password'],
        "password_confirm" => $data["password_confirm"],
        "password_token" => ""
      ]);
    }
    return $this->getErrorState($state);
  }
}
