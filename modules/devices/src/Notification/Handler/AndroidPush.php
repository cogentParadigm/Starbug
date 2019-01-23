<?php
namespace Starbug\Devices\Notification\Handler;

use Starbug\Devices\Notification\HandlerInterface;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\SettingsInterface;
use Psr\Log\LoggerInterface;

class AndroidPush implements HandlerInterface {
  public function __construct(DatabaseInterface $db, SettingsInterface $settings, LoggerInterface $logger, $apiKey) {
    $this->db = $db;
    $this->settings = $settings;
    $this->logger = $logger;
    $this->apiKey = $apiKey;
  }

  public function deliver($owner, $type, $subject, $body, $data = []) {
    $devices = $this->db->query("devices")
      ->condition("platform", "android")
      ->condition("owner", $owner['id'])->all();
    foreach ($devices as $device) {
      $this->push($device['token'], $subject, $data, $device['environment']);
    }
  }

  public function push($token, $subject, $data) {
    $headers = [
      'Authorization: key=' . $this->apiKey,
      'Content-Type: application/json'
    ];
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $this->encodeMessage($token, $subject, $data));
    $result = curl_exec($handle);
    if (curl_errno($handle)) {
      $this->logger->error("FCM error: " . curl_error($handle));
    }
    curl_close($handle);
    $this->logger->info("FCM push: ".$result);
  }

  protected function encodeMessage($tokens, $subject, $data) {
    $data["alert"] = $subject;
    $post = [
      'to'  => $tokens,
      'notification' => $data + [
        'priority' => 'high',
        "title" => $this->settings->get("site_name"),
        "body" => $subject,
        "sound" => "default"
      ],
      'data' => $data
    ];
    return json_encode($post);
  }
}
