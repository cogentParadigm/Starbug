<?php
namespace Starbug\Notifications;

interface NotificationManagerInterface extends HandlerInterface {
  public function queue($recipientType, $notification, $data = []);
  public function process($id = false);
}
