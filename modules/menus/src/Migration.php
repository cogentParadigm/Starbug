<?php
namespace Starbug\Menus;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $this->schema->addTable(["menus", "groups" => true, "label_select" => "CONCAT(menus.menu, ': ', menus.content)"],
      ["menu", "type" => "string", "length" => "32", "list" => "true", "display" => "false"],
      ["parent", "type" => "int", "default" => "0", "materialized_path" => "menu_path"],
      ["href", "type" => "string", "length" => "255", "label" => "URL", "default" => ""],
      ["content", "type" => "string", "length" => "255", "default" => ""],
      ["icon", "type" => "string", "default" => ""],
      ["target", "type" => "string", "default" => ""],
      ["template", "type" => "string", "length" => "128", "default" => ""],
      ["position", "type" => "int", "ordered" => "menu parent", "default" => "0"],
      ["menu_path", "type" => "string", "length" => "255", "default" => "", "display" => "false"]
    );

    // admin menu
    $this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/users"], ["content" => "Users", "icon" => "fa-users"]);
    $content = $this->schema->addRow("menus", ["menu" => "admin", "content" => "Content"]);
    $this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/menus"], ["parent" => $content, "content" => "Menus", "icon" => "fa-list"]);
    $this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/email-templates"], ["parent" => $content, "content" => "Email Templates", "icon" => "fa-envelope"]);
  }
}
