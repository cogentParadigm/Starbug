<?php
namespace Starbug\Queues;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable(["queues"],
      ["queue", "type" => "string"],
      ["worker", "type" => "string"],
      ["data", "type" => "text", "default" => ""],
      ["position", "type" => "int", "ordered" => "queue", "default" => "0"],
      ["status", "type" => "string", "default" => "ready"],
      ["message", "type" => "text", "default" => ""]
    );
  }
}
