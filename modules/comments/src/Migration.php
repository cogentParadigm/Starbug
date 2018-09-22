<?php
namespace Starbug\Comments;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable("comments",
      ["name", "type" => "string", "length" => "255"],
      ["email", "type" => "string", "length" => "255"],
      ["comment", "type" => "text"]
    );
  }
}
