<?php
namespace Starbug\Notifications\Handler;

use Starbug\Notifications\HandlerInterface;
use Starbug\Emails\MailerInterface;
use Starbug\Db\DatabaseInterface;

class Email implements HandlerInterface {
  public function __construct(MailerInterface $mailer, DatabaseInterface $db) {
    $this->mailer = $mailer;
    $this->db = $db;
  }
  public function deliver($owner, $notification, $data = []) {
    $notification = $this->db->query("notifications")
      ->condition("notifications.name", $notification)
      ->select("notifications.email_template.name as template")
      ->one();
    if (empty($owner["email"]) && !empty($owner["email_recipient"])) {
      $owner["email"] = $owner["email_recipient"];
    }
    $this->mailer->send($data + ["template" => $notification["template"], "to" => $owner["email"]], $data);
  }
}
