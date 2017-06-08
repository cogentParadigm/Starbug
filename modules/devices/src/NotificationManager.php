<?php
namespace Starbug\Devices;
use Starbug\Core\ModelFactoryInterface;
class NotificationManager implements NotificationHandlerInterface {
  protected $handlers = array();
  public function __construct(ModelFactoryInterface $models) {
    $this->notifications = $models->get("notifications");
  }
  public function addHandler($name, NotificationHandlerInterface $handler) {
    $this->handlers[$name] = $handler;
  }
  public function getHandler($name) {
    return $this->Handlers[$name];
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    // store notification
    $notification = ["users_id" => $owner['id'], "type" => $type, "subject" => $subject, "body" => $body];
    $this->notifications->store($notification);
    // send notification for each handler
    foreach ($this->handlers as $handlerName => $handler) {
      if ($owner[$type."_".$handlerName]) {
        $handler->deliver($owner, $type, $subject, $body, $data);
      }
    }
  }
}
