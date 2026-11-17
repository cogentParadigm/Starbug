<?php
namespace Starbug\Notifications\Admin\Display;

use Starbug\Dojo\Display\GridDisplay;

class NotificationsGrid extends GridDisplay {
  public $model = "notifications";
  public $action = "admin";
  public function buildDisplay($options) {
    $this->add(["name"]);
    $this->add(["channel_label", "label" => "Channel"]);
    $this->update(["row_options", "canDelete" => "false"]);
  }
}
