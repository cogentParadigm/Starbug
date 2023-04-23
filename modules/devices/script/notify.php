<?php
namespace Starbug\Devices;

use Starbug\Db\DatabaseInterface;

class NotifyCommand {
  public function __construct(NotificationManagerInterface $notifications, DatabaseInterface $db) {
    $this->notifications = $notifications;
    $this->db = $db;
  }
  public function run($argv) {
    $uid = $argv[0];
    $user = $this->db->query("users")->condition("id", $uid)->one();
    $this->notifications->deliver($user, "system", "Hello World", "This is a test message.");
  }
}
