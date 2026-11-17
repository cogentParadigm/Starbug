<?php
namespace Starbug\Notifications\Script;

use Starbug\Notifications\NotificationManagerInterface;

class ProcessNotifications {
  public function __construct(NotificationManagerInterface $notifications) {
    $this->notifications = $notifications;
  }
  public function __invoke($argv) {
    $this->notifications->process();
  }
}
