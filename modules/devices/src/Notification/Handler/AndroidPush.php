<?php
namespace Starbug\Devices\Notification\Handler;

use Starbug\Devices\Notification\HandlerInterface;
use Starbug\Core\DatabaseInterface;
use Psr\Log\LoggerInterface;

class AndroidPush implements HandlerInterface {
  public function __construct(DatabaseInterface $db, LoggerInterface $logger, $apiKey) {
    $this->db = $db;
    $this->logger = $logger;
    $this->apiKey = $apiKey;
  }

  public function deliver($owner, $type, $subject, $body, $data = []) {
    $devices = $this->db->query("devices")
      ->condition("platform", "android")
      ->condition("owner", $owner['id'])->all();
    $this->push(array_column($devices, "token"), $subject, $body, $data);
  }

  public function push($tokens, $subject, $body, $data) {
    $headers = [
      'Authorization: key=' . $this->apiKey,
      'Content-Type: application/json'
    ];
    $handle = curl_init();
    curl_setopt($handle, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($handle, CURLOPT_POST, true);
    curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_POSTFIELDS, $this->encodeMessage($tokens, $subject, $body, $data));
    $results = curl_exec($handle);
    if (curl_errno($handle)) {
      $this->logger->error("FCM error: " . curl_error($handle));
    }
    curl_close($handle);
    if ($results) {
      $this->logger->info("FCM push: ".$results);
      if (!is_array($tokens)) {
        $tokens = [$tokens];
      }
      $results = json_decode($results, true);
      foreach ($results["results"] as $idx => $result) {
        if (!empty($result["error"])) {
          $this->logger->error("FCM error " . $tokens[$idx] . " " . $result["error"]);
          if ($result["error"] == "NotRegistered") {
            $this->db->query("devices")->condition("token", $tokens[$idx])->delete();
          }
        }
      }
    }
  }

  protected function encodeMessage($tokens, $subject, $body, $data) {
    $data["alert"] = $subject;
    $post = [
      'notification' => $data + [
        'priority' => 'high',
        "title" => $subject,
        "body" => $body,
        "sound" => "default"
      ],
      'data' => $data
    ];
    if (is_array($tokens)) {
      $post["registration_ids"] = $tokens;
    } else {
      $post["to"] = $tokens;
    }
    return json_encode($post);
  }
}
