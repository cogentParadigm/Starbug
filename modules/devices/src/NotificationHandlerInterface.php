<?php
namespace Starbug\Devices;

interface NotificationHandlerInterface {
  public function deliver($owner, $type, $subject, $body, $data = []);
}
