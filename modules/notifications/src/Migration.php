<?php

namespace Starbug\Notifications;

use Starbug\Bundle\Bundle;
use Starbug\Db\Schema\AbstractMigration;
use Starbug\Db\Schema\SchemaInterface;

class Migration extends AbstractMigration {
  public function __construct(SchemaInterface $schema, $handlers = [], $channels = [], $defaultHandlers = [], $defaultChannels = []) {
    $this->schema = $schema;
    $this->handlers = $handlers;
    $this->channels = $channels;
    $this->defaultHandlers = $defaultHandlers;
    $this->defaultChannels = $defaultChannels;
  }
  public function up() {
    $this->schema->addTable("users",
      ["notification_batch_frequency", "type" => "int", "default" => "0"]
    );
    $this->schema->addTable(["notifications", "label_select" => "notifications.name"],
      ["name", "type" => "string"],
      ["channel", "type" => "string"],
      ["email_template", "type" => "int", "references" => "email_templates id", "null" => true, "default" => "NULL"]
    );
    $this->schema->addTable("notifications_queue",
      ["owner", "type" => "int", "null" => true, "references" => "users id", "owner" => true, "delete" => "set null", "update" => "cascade", "optional" => true],
      ["notifications_id", "type" => "int", "references" => "notifications id", "alias" => "%name%"],
      ["recipient_type", "type" => "string"],
      ["send_date", "type" => "datetime", "default" => "0000-00-00 00:00:00"],
      ["sent", "type" => "datetime", "null" => true, "default" => "NULL"],
      ["users_id", "type" => "int", "references" => "users id", "null" => "", "default" => "NULL", "delete" => "cascade", "update" => "cascade"],
      ["read", "type" => "bool", "default" => "0"],
      ["batch_key", "type" => "string", "length" => "255", "default" => ""]
    );
    foreach ($this->handlers as $name) {
      $this->schema->addTable("notifications_queue",
        [$name."_recipient", "type" => "text", "default" => ""],
        [$name."_data", "type" => "text", "default" => ""]
      );
    }
    foreach ($this->channels as $channel) {
      foreach ($this->handlers as $handler) {
        $this->schema->addTable("users",
          [$channel."_".$handler, "type" => "bool", "default" => (in_array($channel, $this->defaultChannels) && in_array($handler, $this->defaultHandlers)) ? "1" : "0"]
        );
      }
    }

    $configuration = new Bundle(["table" => "menus", "keys" => ["menu" => "admin", "content" => "Configuration"]]);
    $this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/notifications"], ["parent" => $configuration, "content" => "Notifications"]);
  }
}
