<?php

namespace Starbug\Devices;

use Starbug\Db\Schema\AbstractMigration;
use Starbug\Db\Schema\SchemaInterface;
use Starbug\Bundle\Bundle;

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
    $this->schema->addTable("devices",
      ["token", "type" => "text"],
      ["platform", "type" => "string", "default" => ""],
      ["user_agent", "type" => "text", "default" => ""],
      ["environment", "type" => "string", "length" => "128", "default" => ""]
    );
    $this->schema->addTable("notifications",
      ["type", "type" => "string", "default" => ""],
      ["subject", "type" => "string", "length" => "128"],
      ["body", "type" => "text", "default" => ""],
      ["send_date", "type" => "datetime", "default" => "0000-00-00 00:00:00"],
      ["sent", "type" => "datetime", "default" => "0000-00-00 00:00:00"],
      ["users_id", "type" => "int", "references" => "users id", "null" => "", "default" => "NULL"],
      ["email", "type" => "string", "length" => "128", "default" => ""],
      ["read", "type" => "bool", "default" => "0"],
      ["batch_key", "type" => "string", "length" => "255", "default" => ""]
    );
    foreach ($this->handlers as $name) {
      $this->schema->addTable("notifications",
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

    $user = new Bundle(["table" => "terms", "keys" => ["taxonomy" => "groups", "slug" => "user"]]);
    $this->schema->addRow("permits", ["related_table" => "devices", "action" => "register", "role" => "everyone", "priv_type" => "table", "user_groups" => $user]);
  }
}
