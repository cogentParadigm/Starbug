<?php

namespace Starbug\Log;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable("error_log",
      ["channel", "type" => "string", "length" => "255"],
      ["level", "type" => "int"],
      ["message", "type" => "text"],
      ["time", "type" => "datetime"]
    );
  }
}
