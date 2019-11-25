<?php
namespace Starbug\App;

use Starbug\Db\Schema\AbstractMigration;
use Starbug\Bundle\Bundle;

class Migration extends AbstractMigration {
  public function up() {
    $admin = new Bundle(["table" => "terms", "keys" => ["taxonomy" => "groups", "slug" => "admin"]]);
    // GLOBAL READ AND WRITE PERMITS FOR ADMIN
    $this->schema->addRow("permits", ["related_table" => "%", "action" => "%", "role" => "everyone", "priv_type" => "%", "user_groups" => $admin]);
    // USER PERMITS
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "login", "role" => "everyone", "priv_type" => "table"]);
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "logout", "role" => "everyone", "priv_type" => "table"]);
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "register", "role" => "everyone", "priv_type" => "table"]);
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "updateProfile", "role" => "self", "priv_type" => "global"]);
    $this->schema->addRow("permits", ["related_table" => "users", "action" => "resetPassword", "role" => "everyone", "priv_type" => "table"]);
  }
}
