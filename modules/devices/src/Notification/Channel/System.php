<?php
namespace Starbug\Devices\Notification\Channel;

use Starbug\Devices\Notification\ChannelInterface;

class System implements ChannelInterface {
  public function getLabel() {
    return "System messages";
  }
  public function canBatchMessages() {
    return true;
  }
  public function batchMessages($messages) {
    return implode("\n<br/><hr><br/>\n", $messages);
  }
  public function hasAccess($user) {
    return true;
  }
  public function isConfigurable($user) {
    return false;
  }
  public function isEnabled($user) {
    return true;
  }
}
