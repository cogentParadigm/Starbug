<?php
namespace Starbug\Devices\Notification\Handler;

use Starbug\Emails\MailerInterface;
use Starbug\Devices\Notification\HandlerInterface;

class Email implements HandlerInterface {
  public function __construct(MailerInterface $mailer) {
    $this->mailer = $mailer;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    $this->mailer->send($data + ["to" => $owner["email"], "subject" => $subject, "body" => $body]);
  }
}
