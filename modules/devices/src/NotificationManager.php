<?php
namespace Starbug\Devices;
use Starbug\Core\ModelFactoryInterface;
class NotificationManager implements NotificationHandlerInterface {
  protected $handlers = array();
  public function __construct(ModelFactoryInterface $models) {
    $this->notifications = $models->get("notifications");
  }
  public function addHandler(NotificationHandlerInterface $handler) {
    $this->handlers[] = $handler;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    // store notification
    $notification = ["users_id" => $owner['id'], "type" => $type, "subject" => $subject, "body" => $body];
    $this->notifications->store($notification);
    // send notification for each handler
    foreach ($this->handlers as $handler) {
      $handler->deliver($owner, $type, $subject, $body, $data);
    }
  }
}
?>
