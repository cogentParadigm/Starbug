<?php
namespace Starbug\Devices\Notification\Handler;

use Starbug\Devices\Notification\HandlerInterface;
use Minishlink\WebPush\WebPush as WebPushClient;
use Minishlink\WebPush\Subscription;
use Starbug\Core\DatabaseInterface;

class WebPush implements HandlerInterface {
  public function __construct(WebPushClient $webpush, DatabaseInterface $db) {
    $this->webpush = $webpush;
    $this->db = $db;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    $devices = $this->db->query("devices")->condition("owner", $owner["id"])->condition("platform", "web")->all();
    foreach ($devices as $device) {
      $result = $this->webpush->sendNotification(
        Subscription::create(json_decode($device["token"], true)),
        json_encode(["subject" => $subject, "body" => $body] + $data),
        true
      );
      if (is_array($result) && $result["expired"] === true) {
        $this->db->query("devices")->condition("id", $device["id"])->delete();
      }
    }
  }
}
