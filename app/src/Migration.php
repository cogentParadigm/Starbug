<?php
namespace Starbug\App;
use Starbug\Db\Schema\AbstractMigration;
use Starbug\Core\Bundle;
class Migration extends AbstractMigration {
	public function up() {
		$admin = new Bundle(["table" => "terms", "keys" => ["taxonomy" => "groups", "slug" => "admin"]]);
		$published = new Bundle(["table" => "terms", "keys" => ["taxonomy" => "statuses", "slug" => "published"]]);
		//GLOBAL READ AND WRITE PERMITS FOR ADMIN
		$this->schema->addRow("permits", ["related_table" => "%", "action" => "%", "role" => "everyone", "priv_type" => "%", "user_groups" => $admin]);
		// URI PERMITS
		$this->schema->addRow("permits", ["related_table" => "uris", "action" => "read", "role" => "groups", "priv_type" => "global", "object_statuses" => $published]);
		$this->schema->addRow("permits", ["related_table" => "menus", "action" => "read", "role" => "groups", "priv_type" => "global", "object_statuses" => $published]);
		// USER PERMITS
		$this->schema->addRow("permits", ["related_table" => "users", "action" => "login", "role" => "everyone", "priv_type" => "table"]);
		$this->schema->addRow("permits", ["related_table" => "users", "action" => "logout", "role" => "everyone", "priv_type" => "table"]);
		$this->schema->addRow("permits", ["related_table" => "users", "action" => "register", "role" => "everyone", "priv_type" => "table"]);
		$this->schema->addRow("permits", ["related_table" => "users", "action" => "update_profile", "role" => "self", "priv_type" => "global"]);
		$this->schema->addRow("permits", ["related_table" => "users", "action" => "reset_password", "role" => "everyone", "priv_type" => "table"]);
	}
}
?>
