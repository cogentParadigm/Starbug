<?php
namespace Starbug\Notifications\Handler;

use Starbug\Notifications\HandlerInterface;

class Aggregate implements HandlerInterface {
  protected $handlers = [];
  public function addHandler($name, HandlerInterface $handler) {
    $this->handlers[$name] = $handler;
  }
  public function getHandler($name) {
    return $this->handlers[$name];
  }
  public function getHandlers() {
    return $this->handlers;
  }
  public function deliver($owner, $notification, $data = []) {
    foreach ($this->handlers as $handler) {
      $handler->deliver($owner, $notification, $data);
    }
  }
}
