<?php
namespace Starbug\Devices;

use Starbug\Devices\Notification\HandlerInterface;

interface NotificationManagerInterface extends HandlerInterface {
  public function queue($owner, $type, $subject, $body, $data = []);
  public function process();
}
