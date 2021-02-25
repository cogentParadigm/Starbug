<?php
namespace Starbug\Users\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\MailerInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\Operation\Save;

class CreateUser extends Save {
  public function __construct(ModelFactoryInterface $models, MailerInterface $mailer) {
    $this->models = $models;
    $this->mailer = $mailer;
  }
  public function handle(array $user, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    $this->store($user);
    $state = $this->getErrorState($state);
    if (!$this->errors() && empty($user["id"])) {
      $uid = $this->insert_id;
      $data = ["user" => $this->load($uid)];
      $data['user']['password'] = $user["password"] ?? "";
      $this->mailer->send(["template" => "Account Creation", "to" => $user["email"]], $data);
    }
    return $state;
  }
}
