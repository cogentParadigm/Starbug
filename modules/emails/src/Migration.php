<?php
namespace Starbug\Emails;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable("email_templates",
      ["name", "type" => "string", "length" => "128", "list" => true],
      ["subject", "type" => "string", "length" => "155"],
      ["from", "type" => "string", "length" => "255", "default" => ""],
      ["from_name", "type" => "string", "length" => "128", "default" => ""],
      ["cc", "type" => "text", "default" => ""],
      ["bcc", "type" => "text", "default" => ""],
      ["body", "type" => "text", "class" => "rich-text"]
    );
  }
}
