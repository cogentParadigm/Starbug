<?php
namespace Starbug\Devices;
use Starbug\Core\MailerInterface;
class EmailNotificationHandler implements NotificationHandlerInterface {
  public function __construct(MailerInterface $mailer) {
    $this->mailer = $mailer;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    if ($owner[$type."_email"]) {
      $this->mailer->send(["to" => $owner['email'], "subject" => $subject, "body" => $body]);
    }
  }
}