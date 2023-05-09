<?php
namespace Starbug\Files;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable(
      ["files_categories", "label_select" => "files_categories.name"],
      ["name", "type" => "string", "unique" => ""],
      ["slug", "type" => "string", "slug" => "name"],
      ["position", "type" => "int", "ordered" => ""]
    );
    $this->schema->addTable(["files", "label_select" => "files.filename"],
      ["location", "type" => "string", "length" => "128", "default" => "default"],
      ["filename", "type" => "string", "length" => "128"],
      ["category", "type" => "int", "references" => "files_categories id", "alias" => "%slug%"],
      ["mime_type", "type" => "string", "length" => "128", "display" => false],
      ["size", "type" => "int", "default" => "0", "display" => false],
      ["caption", "type" => "string", "length" => "255", "display" => false]
    );

    $this->schema->addRow("files_categories", ["name" => "Uncategorized"]);
  }
}
