<?php
namespace Starbug\App;

use Starbug\Db\Schema\AbstractMigration;
use Starbug\Bundle\Bundle;

class Migration extends AbstractMigration {
  public function up() {
    $admin = new Bundle(["table" => "groups", "keys" => ["slug" => "admin"]]);
    // GLOBAL READ AND WRITE PERMITS FOR ADMIN
    $this->schema->addRow("permits", ["related_table" => "%", "action" => "%", "role" => "everyone", "priv_type" => "%", "user_groups" => $admin]);
  }
}
