<?php
namespace Starbug\Notifications;

interface HandlerInterface {
  public function deliver($owner, $notification, $data = []);
}
