<?php
namespace Starbug\Devices\Notification\Handler;

use Starbug\Devices\Notification\HandlerInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\SettingsInterface;
use Psr\Log\LoggerInterface;

class ApplePush implements HandlerInterface {
  protected $environments = [
    "production" => "ssl://gateway.push.apple.com:2195",
    "development" => "ssl://gateway.sandbox.push.apple.com:2195"
  ];
  protected $certificate_directory;
  protected $passphrase;
  public function __construct(ModelFactoryInterface $models, SettingsInterface $settings, LoggerInterface $logger) {
    $this->models = $models;
    $this->settings = $settings;
    $this->logger = $logger;
    $this->certificate_directory = $this->settings->get("apn_cert_path");
    // $this->passphrase = $this->settings->get("apn_cert_pass");
  }

  public function deliver($owner, $type, $subject, $body, $data = []) {
    $devices = $this->models->get("devices")->query()
                  ->condition("platform", "ios")->condition("owner", $owner['id'])->all();
    foreach ($devices as $device) {
      $this->push($device['token'], $subject, $data, $device['environment']);
    }
  }

  public function push($tokens, $message, $data = [], $environment = "production") {
    // open a connection
    $fp = $this->openConnection($environment);
    // iterate over tokens
    if (!is_array($tokens)) $tokens = [$tokens];
    foreach ($tokens as $token) {
      // encode the message
      $message = $this->encodeMessage($token, $message, $data);
      // transmit it to the server
      $result = fwrite($fp, $message, strlen($message));
      // check for errors
      $fail = $this->checkAppleErrorResponse($fp);
      if (!$fail) {
        $this->logger->info("Push notification sent", ["tokens" => $tokens, "message" => $message]);
      }
    }
    // close the connection
    fclose($fp);
  }

  protected function encodeMessage($deviceToken, $message, $data = []) {
    if (is_string($message)) $message = ["alert" => $message, "badge" => 1, "sound" => "default"];
    $message = $message + $data;
    $payload = json_encode(['aps' => $message]);
    // build the binary notification
    $bin = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
    return $bin;
  }

  protected function openConnection($environment = "production") {
    $cert_path = $this->certificate_directory . $environment . '.pem';
    $ctx = stream_context_create();
    stream_context_set_option($ctx, 'ssl', 'local_cert', $cert_path);
    // stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
    $fp = stream_socket_client($this->environments[$environment], $err, $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
    stream_set_blocking($fp, 0);
    if (!$fp) $this->logger->error("Failed to connect: $err $errstr" . PHP_EOL);
    return $fp;
  }

  protected function checkAppleErrorResponse($fp) {
    // byte1=always 8, byte2=StatusCode, bytes3,4,5,6=identifier(rowID). Should return nothing if OK.
    $apple_error_response = fread($fp, 6);
    // NOTE: Make sure you set stream_set_blocking($fp, 0) or else fread will pause your script and wait forever when there is no response to be sent.
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
