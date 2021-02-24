<?php
namespace Starbug\Users\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\Operation\Save;

class ResetPassword extends Save {
  public function handle(BundleInterface $data, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    $data = $data->get();
    $user = $this->query()->condition("email", $data['email'])->one();
    if (empty($user['password_token']) || $user['password_token'] != $data['token']) {
      $this->error("Your password reset request could not be verified. Please follow the link you were emailed.", "email");
    }
    if (empty($data["password"])) {
      $this->error("This field is required", "password");
    }
    if (!$this->errors()) {
      $this->store([
        "id" => $user['id'],
        "password" => $data['password'],
        "password_confirm" => $data["password_confirm"],
        "password_token" => ""
      ]);
    }
    return $this->getErrorState($state);
  }
}
