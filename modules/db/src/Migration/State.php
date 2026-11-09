<?php
namespace Starbug\Db\Migration;

use Starbug\Db\Schema\AbstractMigration;

class State extends AbstractMigration {
  public function up() {
    $this->schema->addTable(["state"],
      ["name", "type" => "string", "length" => "255"],
      ["value", "type" => "text", "default" => ""],
      ["expires", "type" => "datetime", "default" => "NULL", "null" => true]
    );
    $this->schema->addIndex("state", ["name"]);
  }
}
