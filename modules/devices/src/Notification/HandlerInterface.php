<?php
namespace Starbug\Devices\Notification;

interface HandlerInterface {
  public function deliver($owner, $type, $subject, $body, $data = []);
}
