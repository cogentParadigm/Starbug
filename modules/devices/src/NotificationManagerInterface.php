<?php
namespace Starbug\Devices;

interface NotificationManagerInterface extends NotificationHandlerInterface {
  public function queue($owner, $type, $subject, $body, $data = []);
  public function process();
}
