<?php
namespace Starbug\Devices\Notification\Handler;

use Starbug\Db\DatabaseInterface;
use Starbug\Devices\Notification\HandlerInterface;
use Psr\Log\LoggerInterface;

class ApplePush implements HandlerInterface {
  protected $environments = [
    "production" => "ssl://gateway.push.apple.com:2195",
    "development" => "ssl://gateway.sandbox.push.apple.com:2195"
  ];
  protected $certificateDirectory;
  protected $passphrase;
  public function __construct(DatabaseInterface $db, LoggerInterface $logger, $certificateDirectory, $passphrase = false) {
    $this->db = $db;
    $this->logger = $logger;
    $this->certificateDirectory = $certificateDirectory;
    $this->passphrase = $passphrase;
  }

  public function deliver($owner, $type, $subject, $body, $data = []) {
    $devices = $this->db->query("devices")->condition("platform", "ios")->condition("owner", $owner['id'])->all();
    $environments = [];
    foreach ($devices as $device) {
      $environments[$device["environment"]][] = $device["token"];
    }
    foreach ($environments as $environment => $tokens) {
      $this->push($tokens, $subject, $data, $environment);
    }
  }

  public function push($tokens, $message, $data = [], $environment = "production") {
    // open a connection
    $handle = $this->openConnection($environment);
    // iterate over tokens
    if (!is_array($tokens)) {
      $tokens = [$tokens];
    }
    foreach ($tokens as $token) {
      // encode the message
      $message = $this->encodeMessage($token, $message, $data);
      // transmit it to the server
      fwrite($handle, $message, strlen($message));
      // check for errors
      $fail = $this->checkAppleErrorResponse($handle);
      if (!$fail) {
        $this->logger->info("Push notification sent", ["tokens" => $tokens, "message" => $message]);
      }
    }
    // close the connection
    fclose($handle);
  }

  protected function encodeMessage($deviceToken, $message, $data = []) {
    if (is_string($message)) {
      $message = ["alert" => $message, "badge" => 1, "sound" => "default"];
    }
    $message = $message + $data;
    $payload = json_encode(['aps' => $message]);
    // build the binary notification
    $bin = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
    return $bin;
  }

  protected function openConnection($environment = "production") {
    $cert_path = $this->certificateDirectory . $environment . '.pem';
    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'local_cert', $cert_path);
    if (false !== $this->passphrase) {
      stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
    }
    $handle = stream_socket_client($this->environments[$environment], $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    stream_set_blocking($handle, 0);
    if (!$handle) {
      $this->logger->error("Failed to connect: $err $errstr" . PHP_EOL);
    }
    return $handle;
  }

  protected function checkAppleErrorResponse($handle) {
    // byte1=always 8, byte2=StatusCode, bytes3,4,5,6=identifier(rowID). Should return nothing if OK.
    $apple_error_response = fread($handle, 6);
    // NOTE: Make sure you set stream_set_blocking($handle, 0) or else fread will pause your script and wait forever when there is no response to be sent.
    if ($apple_error_response) {
      // unpack the error response (first byte 'command" should always be 8)
      $error_response = unpack('Ccommand/Cstatus_code/Nidentifier', $apple_error_response);
      if ($error_response['status_code'] == '0') {
        $error_response['status_code'] = '0-No errors encountered';
      } elseif ($error_response['status_code'] == '1') {
        $error_response['status_code'] = '1-Processing error';
      } elseif ($error_response['status_code'] == '2') {
        $error_response['status_code'] = '2-Missing device token';
      } elseif ($error_response['status_code'] == '3') {
        $error_response['status_code'] = '3-Missing topic';
      } elseif ($error_response['status_code'] == '4') {
        $error_response['status_code'] = '4-Missing payload';
      } elseif ($error_response['status_code'] == '5') {
        $error_response['status_code'] = '5-Invalid token size';
      } elseif ($error_response['status_code'] == '6') {
        $error_response['status_code'] = '6-Invalid topic size';
      } elseif ($error_response['status_code'] == '7') {
        $error_response['status_code'] = '7-Invalid payload size';
      } elseif ($error_response['status_code'] == '8') {
        $error_response['status_code'] = '8-Invalid token';
      } elseif ($error_response['status_code'] == '255') {
        $error_response['status_code'] = '255-None (unknown)';
      } else {
        $error_response['status_code'] = $error_response['status_code'] . '-Not listed';
      }
      $this->logger->error('<br><b>+ + + + + + ERROR</b> Response Command:<b>' . $error_response['command'] . '</b>&nbsp;&nbsp;&nbsp;Identifier:<b>' . $error_response['identifier'] . '</b>&nbsp;&nbsp;&nbsp;Status:<b>' . $error_response['status_code'] . '</b><br>'.
            'Identifier is the rowID (index) in the database that caused the problem, and Apple will disconnect you from server. To continue sending Push Notifications, just start at the next rowID after this Identifier.<br>');
      return true;
    }
    return false;
  }
}
