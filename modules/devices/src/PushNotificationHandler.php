<?php
namespace Starbug\Devices;

use Starbug\Core\MailerInterface;

class PushNotificationHandler implements NotificationHandlerInterface {
  public function __construct(ApplePushNotificationHandler $apple) {
    $this->apple = $apple;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    $this->apple->deliver($owner, $type, $subject, $body, $data);
  }
}
