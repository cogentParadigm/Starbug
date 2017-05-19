<?php

namespace Starbug\Devices;

use Starbug\Db\Schema\AbstractMigration;
use Starbug\Core\Bundle;

class Migration extends AbstractMigration {

  public function up() {
    $this->schema->addTable("devices",
      ["token", "type" => "text"],
      ["platform", "type" => "string", "default" => ""],
      ["user_agent", "type" => "text", "default" => ""],
      ["environment", "type" => "string", "length" => "128", "default" => ""]
    );
    $this->table("notifications",
      ["type", "type" => "string", "default" => ""],
      ["subject", "type" => "string", "length" => "128"],
      ["body", "type" => "text", "default" => ""],
      ["send_date", "type" => "datetime", "default" => "0000-00-00 00:00:00"],
      ["sent", "type" => "datetime", "default" => "0000-00-00 00:00:00"],
      ["users_id", "type" => "int", "references" => "users id", "null" => "", "default" => "NULL"]
    );
  }
}
