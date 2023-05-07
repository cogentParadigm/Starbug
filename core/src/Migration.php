<?php
namespace Starbug\Core;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    // This adds a table to the schema, The Schemer builds up a schema with all of the migrations that are to be run, and then updates the db
    $this->schema->addTable(["users", "label_select" => "CONCAT(first_name, ' ', last_name)", "groups" => true],
      ["first_name", "type" => "string", "length" => "64", "list" => "true"],
      ["last_name", "type" => "string", "length" => "64", "list" => "true"],
      ["email", "type" => "string", "length" => "128", "unique" => "", "null" => true],
      ["password", "type" => "password", "confirm" => "password_confirm", "optional_update" => ""],
      ["last_visit", "type" => "datetime", "default" => "0000-00-00 00:00:00", "list" => "true", "display" => "false"],
      ["password_token", "type" => "string", "default" => ""]
    );
    $this->schema->addTable(["sessions"],
      ["users_id", "type" => "int", "references" => "users id", "update" => "cascade", "delete" => "cascade"],
      ["token", "type" => "string"],
      ["expires", "type" => "datetime"]
    );
    $this->schema->addTable(["terms", "label_select" => "terms.term"],
      ["term", "type" => "string", "length" => "128"],
      ["slug", "type" => "string", "length" => "128", "unique" => "taxonomy parent", "display" => "false", "default" => "", "slug" => "term"],
      ["description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
      ["taxonomy", "type" => "string", "views" => "taxonomies", "input_type" => "hidden"],
      ["parent", "type" => "int", "default" => "0", "input_type" => "category_select", "readonly" => "", "materialized_path" => "term_path"],
      ["position", "type" => "int", "ordered" => "taxonomy parent", "display" => "false"],
      ["term_path", "type" => "string", "length" => "255", "default" => "", "display" => "false"]
    );
    $this->schema->addColumn("users",
      ["groups", "type" => "terms", "taxonomy" => "groups", "user_access" => true, "optional" => true, "groups" => true]
    );
    $this->schema->addUniqueIndex("users_groups", ["users_id", "groups_id"]);
    $this->schema->addTable(["permits", "list" => "all", "groups" => true],
      ["role", "type" => "string", "length" => "30"],
      ["who", "type" => "int", "default" => "0"],
      ["action", "type" => "string", "length" => "100"],
      ["priv_type", "type" => "string", "length" => "30", "default" => "table"],
      ["related_table", "type" => "string", "length" => "100"],
      ["related_id", "type" => "int", "default" => "0"]
    );

    // groups
    $this->schema->addRow("terms", ["taxonomy" => "groups", "term" => "User"]);
    $this->schema->addRow("terms", ["taxonomy" => "groups", "term" => "Admin"]);
  }
}
