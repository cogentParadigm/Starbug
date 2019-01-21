<?php
namespace Starbug\Devices\Notification\Handler;

use Starbug\Devices\Notification\HandlerInterface;
use Starbug\Core\MailerInterface;

class Push implements HandlerInterface {
  public function __construct(ApplePush $apple) {
    $this->apple = $apple;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    $this->apple->deliver($owner, $type, $subject, $body, $data);
  }
}
