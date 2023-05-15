<?php
namespace Starbug\Products\Admin;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $store = $this->schema->addRow("menus", ["menu" => "admin", "content" => "Store"]);
    $this->schema->addRow(
      "menus",
      ["menu" => "admin", "href" => "admin/products"],
      ["parent" => $store, "content" => "Products"]
    );
    $this->schema->addRow(
      "menus",
      ["menu" => "admin", "href" => "admin/product-types"],
      ["parent" => $store, "content" => "Product Types"]
    );
    $this->schema->addRow(
      "menus",
      ["menu" => "admin", "href" => "admin/product-categories"],
      ["parent" => $store, "content" => "Product Categories"]
    );
  }
}
