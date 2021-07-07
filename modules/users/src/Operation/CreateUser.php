<?php
namespace Starbug\Users\Operation;

use Starbug\Bundle\BundleInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\MailerInterface;
use Starbug\Core\Operation\Save;

class CreateUser extends Save {
  public function __construct(DatabaseInterface $db, MailerInterface $mailer) {
    $this->db = $db;
    $this->mailer = $mailer;
  }
  public function handle(array $user, BundleInterface $state): BundleInterface {
    $this->setModel("users");
    $this->db->store("users", $user);
    $state = $this->getErrorState($state);
    if (!$this->db->errors() && empty($user["id"])) {
      $uid = $this->insert_id;
      $data = ["user" => $this->db->get("users", $uid)];
      $data['user']['password'] = $user["password"] ?? "";
      $this->mailer->send(["template" => "Account Creation", "to" => $user["email"]], $data);
    }
    return $state;
  }
}
