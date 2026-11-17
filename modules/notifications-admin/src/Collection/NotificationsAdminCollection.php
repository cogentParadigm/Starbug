<?php
namespace Starbug\Notifications\Admin\Collection;

use Starbug\Admin\Db\Query\AdminCollection;
use Starbug\Notifications\NotificationManagerInterface;
use Starbug\Db\DatabaseInterface;

class NotificationsAdminCollection extends AdminCollection {
  protected $model = "notifications";
  public function __construct(DatabaseInterface $db, NotificationManagerInterface $notifications) {
    $this->db = $db;
    $this->notifications = $notifications;
  }
  public function filterRows($rows) {
    foreach ($rows as &$row) {
      $row["channel_label"] = $this->notifications->getChannel($row["channel"])->getLabel();
    }
    return $rows;
  }
}
