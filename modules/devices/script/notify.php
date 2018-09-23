<?php
namespace Starbug\Devices;

use Starbug\Core\IdentityInterface;

class NotifyCommand {
  public function __construct(NotificationManagerInterface $notifications, IdentityInterface $user) {
    $this->notifications = $notifications;
    $this->user = $user;
  }
  public function run($argv) {
    $uid = $argv[0];
    $user = $this->user->loadUser($uid);
    $this->notifications->deliver($user, "test", "Hello World", "This is a test message.");
  }
}
