<?php
namespace Starbug\Notifications\Admin\Display;

use Starbug\Display\FormDisplay;
use Starbug\Notifications\NotificationManagerInterface;

class NotificationsForm extends FormDisplay {
  public $model = "notifications";
  public function setNotificationManager(NotificationManagerInterface $notifications) {
    $this->notifications = $notifications;
  }
  public function buildDisplay($options) {
    $channels = $this->notifications->getChannels();
    foreach ($channels as $channelName => $channel) {
      $channels[$channelName] = $channel->getLabel();
    }
    $readOnly = empty($options["id"]) ? [] : ["readonly" => "readonly"];
    $this->add(["name"] + $readOnly);
    $this->add(["channel", "input_type" => "select", "options" => array_values($channels), "values" => array_keys($channels)]);
    $handlers = $this->notifications->getHandlers();
    if (isset($handlers["email"])) {
      $this->add(["email_template", "input_type" => "text", "data-dojo-type" => "sb/form/Select", "data-dojo-props" => "model: 'email_templates', searchable: true, query: {optional: ''}"]);
    }
    if (isset($handlers["push"])) {
      $this->add(["push_template", "input_type" => "text", "data-dojo-type" => "sb/form/Select", "data-dojo-props" => "model: 'push_templates', searchable: true, query: {optional: ''}"]);
    }
    if (isset($handlers["text"])) {
      $this->add(["text_template", "input_type" => "text", "data-dojo-type" => "sb/form/Select", "data-dojo-props" => "model: 'text_templates', searchable: true, query: {optional: ''}"]);
    }
  }
}
