<?php
namespace Starbug\Devices;
use Starbug\Core\DatabaseInterface;
class NotificationManager implements NotificationManagerInterface {
  protected $handlers = [];
  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function addHandler($name, NotificationHandlerInterface $handler) {
    $this->handlers[$name] = $handler;
  }
  public function getHandler($name) {
    return $this->handlers[$name];
  }
  public function getHandlers() {
    return $this->handlers;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    $this->queue($owner, $type, $subject, $body, $data);
  }
  public function queue($owner, $type, $subject, $body, $data = []) {
    $now = date("Y-m-d H:i:s");
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        $data[$key] = json_encode($value);
      }
    }
    // store notification
    $notification = ["users_id" => $owner['id'], "type" => $type, "subject" => $subject, "body" => $body, "send_date" => $now] + $data;
    $this->db->store("notifications", $notification);
  }
  public function process() {
    $now = date("Y-m-d H:i:s");
    $hour = intval(date("H"));
    $minute = intval(date("i"));
    $results = $this->db->query("notifications")->condition("send_date", $now, "<=")->condition("sent", "0000-00-00 00:00:00")->all();
    $user_notifications = [];
    foreach ($results as $result) {
      $uid = $result["owner"];
      if (!isset($user_notifications[$uid])) {
        $user_notifications[$uid] = [
          "user" => $this->db->get("users", $uid),
          "notifications" => []
        ];
      }
      $user_notifications[$uid]["notifications"][$result["batch_key"]] = $result;
    }
    foreach ($user_notifications as $uid => $user) {
      $owner = $user["user"];
      $batch = $batchable = false;
      if (!empty($owner["notification_batch_frequency"])) {
        $batchable = true;
        $freq = intval($owner["notification_batch_frequency"]);
        if ($hour % $freq == 0 && $minute == 0) $batch = true;
      }
      foreach ($user["notifications"] as $batch_key => $notifications) {
        if (!empty($batch_key) && $batchable) {
          if ($batch) {
            //it's time to batch
            $body = [];
            foreach ($notifications as $notification) {
              $body[] = $notification["body"];
              $this->db->store("notifications", ["id" => $notification["id"], "sent" => date("Y-m-d H:i:s")]);
            }
            foreach ($this->handlers as $handlerName => $handler) {
              if ($owner[$type."_".$handlerName]) {
                $data = empty($notification[$handlerName."_data"]) ? [] : json_decode($notification[$handlerName."_data"], true);
                $handler->deliver($owner, $notification["type"], $notification["subject"], implode("\n<br/><hr><br/>\n", $body), $data);
              }
            }
          }
          continue;
        }
        foreach ($notifications as $notification) {
          // Send notification for each handler.
          foreach ($this->handlers as $handlerName => $handler) {
            if ($owner[$type."_".$handlerName]) {
              $data = empty($notification[$handlerName."_data"]) ? [] : json_decode($notification[$handlerName."_data"], true);
              $handler->deliver($owner, $notification["type"], $notification["subject"], $notification["body"], $data);
            }
          }
          $this->db->store("notifications", ["id" => $notification["id"], "sent" => date("Y-m-d H:i:s")]);
        }
      }
    }
  }
}
