<?php
namespace Starbug\Notifications\Recipient;

use Starbug\Notifications\NotificationManagerInterface;

class GenericRecipient implements RecipientTypeInterface {
  public function __construct(NotificationManagerInterface $notifications) {
    $this->notifications = $notifications;
  }
  public function getId($notification) {
    return $notification["id"];
  }
  public function getRecipient($notification) {
    $handlers = array_keys($this->notifications->getHandlers());
    $channels = array_keys($this->notifications->getChannels());
    foreach ($handlers as $handler) {
      foreach ($channels as $channel) {
        $notification[$channel."_".$handler] = true;
      }
    }
    return $notification;
  }
}
