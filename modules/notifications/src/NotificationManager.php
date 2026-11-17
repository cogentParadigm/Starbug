<?php
namespace Starbug\Notifications;

use Starbug\Db\DatabaseInterface;
use Psr\Log\LoggerAwareTrait;
use Exception;
use Starbug\Notifications\Queue\ProcessNotificationsWorker;
use Starbug\Notifications\Recipient\RecipientTypeFactoryInterface;
use Starbug\Queue\QueueManagerInterface;

class NotificationManager implements NotificationManagerInterface {
  protected $handlers = [];
  protected $channels = [];

  use LoggerAwareTrait;

  public function __construct(DatabaseInterface $db, RecipientTypeFactoryInterface $recipientTypes, QueueManagerInterface $queues) {
    $this->db = $db;
    $this->recipientTypes = $recipientTypes;
    $this->queues = $queues;
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
  public function deliver($recipientType, $notification, $data = []) {
    $this->queue($recipientType, $notification, $data);
  }
  public function queue($recipientType, $notification, $data = []) {
    $now = date("Y-m-d H:i:s");
    foreach ($data as $key => $value) {
      if (is_array($value)) {
        $data[$key] = json_encode($value);
      }
    }
    $notification = array_merge([
      "notifications_id" => $notification,
      "recipient_type" => $recipientType,
      "send_date" => $now
    ], $data);
    $this->db->store("notifications_queue", $notification);
    if (!$this->db->errors()) {
      $this->queues->put(ProcessNotificationsWorker::class, [
        "id" => $this->db->getInsertId("notifications_queue")
      ]);
    }
  }
  public function process($id = false) {
    $now = date("Y-m-d H:i:s");
    $hour = intval(date("H"));
    $minute = intval(date("i"));
    $results = $this->db->query("notifications_queue")
      ->select(["*", "notifications_id.channel", "notifications_id.name as notification"], "notifications_queue")
      ->condition("send_date", $now, "<=")
      ->condition("sent", "NULL");
    if ($id) {
      $results->condition("notifications_queue.id", $id);
    }
    $results = $results->all();
    if (empty($results)) {
      return false;
    }
    $recipientNotifications = [];
    foreach ($results as $result) {
      $recipientType = $this->recipientTypes->get($result["recipient_type"]);
      $recipientId = $recipientType->getId($result);
      if (!isset($recipientNotifications[$result["recipient_type"]])) {
        $recipientNotifications[$result["recipient_type"]] = [];
      }
      if (!isset($recipientNotifications[$result["recipient_type"]][$recipientId])) {
        $recipientNotifications[$result["recipient_type"]][$recipientId] = [
          "recipient" => $recipientType->getRecipient($result),
          "notifications" => []
        ];
      }
      $recipientNotifications[$result["recipient_type"]][$recipientId]["notifications"][$result["batch_key"]][] = $result;
    }
    foreach ($recipientNotifications as $type => $recipients) {
      foreach ($recipients as $rid => $recipientData) {
        $recipient = $recipientData["recipient"];
        $batch = $batchable = false;
        if (!empty($recipient["notification_batch_frequency"])) {
          $batchable = true;
          $freq = intval($recipient["notification_batch_frequency"]);
          if ($hour % $freq == 0 && $minute == 0) $batch = true;
        }
        foreach ($recipientData["notifications"] as $batch_key => $notifications) {
          /*
          * Fix batching methodology which is broken now that content is expected to come from a template.
          */
          if (false && !empty($batch_key) && $batchable) {
            if ($batch) {
              // it's time to batch
              $body = [];
              foreach ($notifications as $notification) {
                $body[] = $notification["body"];
                $this->db->store("notifications_queue", ["id" => $notification["id"], "sent" => date("Y-m-d H:i:s")]);
              }
              foreach ($this->handlers as $handlerName => $handler) {
                if ($recipient[$notification["channel"]."_".$handlerName]) {
                  $data = empty($notification[$handlerName."_data"]) ? [] : json_decode($notification[$handlerName."_data"], true);
                  try {
                    $handler->deliver($recipient, $notification["notification"], $notification["subject"], implode("\n<br/><hr><br/>\n", $body), $data);
                  } catch (Exception $e) {
                    $this->error($e->getMessage());
                  }
                }
              }
            }
            continue;
          }
          foreach ($notifications as $notification) {
            // Send notification for each handler.
            foreach ($this->handlers as $handlerName => $handler) {
              if ($recipient[$notification["channel"]."_".$handlerName]) {
                $data = empty($notification[$handlerName."_data"]) ? [] : json_decode($notification[$handlerName."_data"], true);
                try {
                  $handler->deliver($recipient, $notification["notification"], $data);
                } catch (Exception $e) {
                  $this->error($e->getMessage());
                }
              }
            }
            $this->db->store("notifications_queue", ["id" => $notification["id"], "sent" => date("Y-m-d H:i:s")]);
          }
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
