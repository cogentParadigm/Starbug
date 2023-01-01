<?php
namespace Starbug\Imports\Migration;

use Starbug\Db\Schema\AbstractMigration;

class ImportsMigration extends AbstractMigration {
  public function up() {
    $label = "CONCAT(".
     "imports_fields.source, ' => ', imports_fields.destination, ".
     "CASE WHEN update_key=1 THEN ' (update key)' ELSE '' END".
     ")";
    $this->schema->addTable(["imports_fields", "label_select" => $label],
      ["source", "type" => "text"],
      ["destination", "type" => "text"],
      ["update_key", "type" => "bool", "default" => "0"]
    );
    $this->schema->addTable(["imports_transformers", "label_select" => "imports_transformers.type"],
      ["type", "type" => "string", "length" => "255"]
    );
    $this->schema->addTable(["imports_transformers_settings", "label_select" => "imports_transformers_settings.name"],
      ["imports_transformers_id", "type" => "int", "references" => "imports_transformers id"],
      ["name", "type" => "string", "length" => "255"],
      ["value", "type" => "text", "default" => ""]
    );
    $this->schema->addTable(["imports", "label_select" => "imports.name"],
      ["name", "type" => "string", "length" => "128"],
      ["model", "type" => "string", "length" => "128"],
      ["operation", "type" => "string", "length" => "128", "default" => ""],
      ["source", "type" => "int", "references" => "files id", "null" => true],
      ["worksheet", "type" => "string", "default" => ""],
      ["fields", "type" => "imports_fields", "table" => "imports_fields"],
      ["transformers", "type" => "imports_transformers", "table" => "imports_transformers"]
    );
    $this->schema->addTable(["import_groups"],
      ["name", "type" => "string"],
      ["imports", "type" => "imports"],
      ["source", "type" => "int", "references" => "files id", "null" => true, "default" => "NULL"]
    );
  }
}
