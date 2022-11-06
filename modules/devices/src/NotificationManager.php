<?php
namespace Starbug\Devices;

use Starbug\Core\DatabaseInterface;
use Psr\Log\LoggerAwareTrait;
use Exception;
use Starbug\Devices\Notification\HandlerInterface;
use Starbug\Devices\Notification\ChannelInterface;

class NotificationManager implements NotificationManagerInterface {
  protected $handlers = [];
  protected $channels = [];

  use LoggerAwareTrait;

  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }
  public function addHandler($name, HandlerInterface $handler) {
    $this->handlers[$name] = $handler;
  }
  public function getHandler($name) {
    return $this->handlers[$name];
  }
  public function getHandlers() {
    return $this->handlers;
  }
  public function addChannel($name, ChannelInterface $channel) {
    $this->channels[$name] = $channel;
  }
  public function getChannel($name) {
    return $this->channels[$name];
  }
  public function getChannels() {
    return $this->channels;
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
    $notification = ["type" => $type, "subject" => $subject, "body" => $body, "send_date" => $now] + $data;
    if (!empty($owner["id"])) {
      $notification["users_id"] = $owner['id'];
    } elseif (!empty($owner["email"])) {
      $notification["email"] = $owner["email"];
    }
    $this->db->store("notifications", $notification);
  }
  public function process() {
    $now = date("Y-m-d H:i:s");
    $hour = intval(date("H"));
    $minute = intval(date("i"));
    $results = $this->db->query("notifications")->condition("send_date", $now, "<=")->condition("sent", "0000-00-00 00:00:00")->all();
    $user_notifications = [];
    foreach ($results as $result) {
      $uid = empty($result["users_id"]) ? $result["email"] : $result["users_id"];
      if (!isset($user_notifications[$uid])) {
        $user_notifications[$uid] = [
          "user" => is_numeric($uid) ? $this->db->get("users", $uid) : false,
          "notifications" => []
        ];
      }
      $user_notifications[$uid]["notifications"][$result["batch_key"]][] = $result;
    }
    foreach ($user_notifications as $uid => $user) {
      $owner = $user["user"];
      $batch = $batchable = false;
      if ($owner && !empty($owner["notification_batch_frequency"])) {
        $batchable = true;
        $freq = intval($owner["notification_batch_frequency"]);
        if ($hour % $freq == 0 && $minute == 0) {
          $batch = true;
        }
      }
      foreach ($user["notifications"] as $batch_key => $notifications) {
        if (!empty($batch_key) && $batchable) {
          if ($batch) {
            // it's time to batch
            $body = [];
            foreach ($notifications as $notification) {
              $body[] = $notification["body"];
              $this->db->store("notifications", ["id" => $notification["id"], "sent" => date("Y-m-d H:i:s")]);
            }
            foreach ($this->handlers as $handlerName => $handler) {
              if ($owner[$notification["type"]."_".$handlerName]) {
                $data = empty($notification[$handlerName."_data"]) ? [] : json_decode($notification[$handlerName."_data"], true);
                try {
                  $handler->deliver($owner, $notification["type"], $notification["subject"], implode("\n<br/><hr><br/>\n", $body), $data);
                } catch (Exception $e) {
                  $this->error($e->getMessage());
                }
              }
            }
          }
          continue;
        }
        foreach ($notifications as $notification) {
          if ($owner) {
            // Send notification for each handler.
            foreach ($this->handlers as $handlerName => $handler) {
              if ($owner[$notification["type"]."_".$handlerName]) {
                $data = empty($notification[$handlerName."_data"]) ? [] : json_decode($notification[$handlerName."_data"], true);
                try {
                  $handler->deliver($owner, $notification["type"], $notification["subject"], $notification["body"], $data);
                } catch (Exception $e) {
                  $this->error($e->getMessage());
                }
              }
            }
          } else {
            // Anonymous users can only get unbatched emails
            $data = empty($notification["email_data"]) ? [] : json_decode($notification["email_data"], true);
            try {
              $this->handlers["email"]->deliver($uid, $notification["type"], $notification["subject"], $notification["body"], $data);
            } catch (Exception $e) {
              $this->error($e->getMessage());
            }
          }
          $this->db->store("notifications", ["id" => $notification["id"], "sent" => date("Y-m-d H:i:s")]);
        }
      }
    }
  }
  protected function error($message) {
    if (!is_null($this->logger)) {
      $this->logger->error($message);
    }
  }
}
