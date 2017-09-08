<?php

namespace Starbug\Devices;

use Starbug\Db\Schema\AbstractMigration;
use Starbug\Db\Schema\SchemaInterface;

class Migration extends AbstractMigration {
  public function __construct(SchemaInterface $schema, NotificationManager $notifications) {
    $this->schema = $schema;
    $this->notifications = $notifications;
  }
  public function up() {
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
      ["read", "type" => "bool", "default" => "0"],
      ["batch_key", "type" => "string", "length" => "255", "default" => ""]
    );
    $handlers = $this->notifications->getHandlers();
    foreach ($handler as $name => $handler) {
      $this->schema->addTable("notifications",
        [$name."_data", "type" => "text", "default" => ""]
      );
    }
  }
}
