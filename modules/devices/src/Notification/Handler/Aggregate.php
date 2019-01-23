<?php
namespace Starbug\Devices\Notification\Handler;

use Starbug\Devices\Notification\HandlerInterface;

class Aggregate implements HandlerInterface {
  public function addHandler($name, HandlerInterface $handler) {
    $this->handlers[$name] = $handler;
  }
  public function getHandler($name) {
    return $this->handlers[$name];
  }
  public function getHandlers() {
    return $this->handlers;
  }
  public function deliver($owner, $type, $subject, $body, $data = []) {
    foreach ($this->handlers as $handler) {
      $handler->deliver($owner, $type, $subject, $body, $data);
    }
  }
}
