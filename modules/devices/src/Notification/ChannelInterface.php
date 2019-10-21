<?php
namespace Starbug\Devices\Notification;

interface ChannelInterface {
  public function getLabel();
  public function canBatchMessages();
  public function batchMessages($messages);
  public function hasAccess($user);
  public function isConfigurable($user);
  public function isEnabled($user);
}
