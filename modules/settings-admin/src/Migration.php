<?php
namespace Starbug\Settings\Admin;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $configuration = $this->schema->addRow(
      "menus",
      ["menu" => "admin", "content" => "Configuration"],
      ["icon" => "fa-cogs"]
    );
    $this->schema->addRow(
      "menus",
      ["menu" => "admin", "href" => "admin/settings"],
      ["parent" => $configuration, "content" => "Settings", "icon" => "fa-cog"]
    );
  }
}
