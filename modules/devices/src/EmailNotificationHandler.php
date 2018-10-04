<?php
namespace Starbug\Devices;
use Starbug\Core\MailerInterface;
class EmailNotificationHandler implements NotificationHandlerInterface {
  public function __construct(MailerInterface $mailer) {
    $this->mailer = $mailer;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    $this->mailer->send($data + ["to" => $owner["email"], "subject" => $subject, "body" => $body]);
  }
}