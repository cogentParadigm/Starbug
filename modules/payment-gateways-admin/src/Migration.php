<?php
namespace Starbug\Payments\Gateways\Admin;

use Starbug\Db\Schema\AbstractMigration;

class Migration extends AbstractMigration {
  public function up() {
    $store = $this->schema->addRow("menus", ["menu" => "admin", "content" => "Store"]);
    $this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/payment-gateways"], ["parent" => $store, "content" => "Payment Gateways"]);
  }
}
