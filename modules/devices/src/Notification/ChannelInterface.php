<?php
namespace Starbug\Devices\Notification;

interface ChannelInterface {
  public function getLabel();
  public function canBatchMessages();
  public function batchMessages($messages);
}
