<?php
namespace Starbug\Devices\Notification\Handler;

use Starbug\Devices\Notification\HandlerInterface;
use Minishlink\WebPush\WebPush as WebPushClient;
use Starbug\Core\DatabaseInterface;

class WebPush implements HandlerInterface {
  public function __construct(WebPush $webpush, DatabaseInterface $db) {
    $this->webpush = $webpush;
    $this->db = $db;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    $devices = $this->db->query("devices")->condition("owner", $owner["id"])->condition("platform", "web")->all();
    foreach ($devices as $device) {
      $token = json_decode($device["token"]);
      $push_data = ["endpoint" => $token->endpoint];
      foreach ($token->keys as $k => $v) $push_data[$k] = $v;
      $result = $this->webpush->sendNotification(
        $token->endpoint,
        json_encode(["subject" => $subject, "body" => $body] + $data),
        $token->keys->p256dh,
        $token->keys->auth,
        true
      );
      if (is_array($result) && $result["expired"] === true) {
        $this->db->query("devices")->condition("id", $device["id"])->delete();
      }
    }
  }
}
