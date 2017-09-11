<?php
namespace Starbug\Devices;

class ProcessNotificationsCommand {
  public function __construct(NotificationManagerInterface $notifications) {
    $this->notifications = $notifications;
  }
  public function run($argv) {
    $this->notifications->process();
  }
}