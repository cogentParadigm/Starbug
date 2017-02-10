<?php
namespace Starbug\Core;
use Starbug\Db\Schema\AbstractMigration;
class Migration extends AbstractMigration {
	public function up() {
		// This adds a table to the schema, The Schemer builds up a schema with all of the migrations that are to be run, and then updates the db
		$this->schema->addTable(["users", "label_select" => "CONCAT(first_name, ' ', last_name)", "groups" => true],
			["first_name", "type" => "string", "length" => "64", "list" => "true"],
			["last_name", "type" => "string", "length" => "64", "list" => "true"],
			["email", "type" => "string", "length" => "128", "unique" => "", "list" => "true"],
			["password", "type" => "password", "confirm" => "password_confirm", "optional_update" => ""],
			["last_visit", "type" => "datetime", "default" => "0000-00-00 00:00:00", "list" => "true", "display" => "false"]
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
			["groups", "type" => "terms", "taxonomy" => "groups", "user_access" => true, "optional" => true]
		);
		$this->schema->addTable(["permits", "list" => "all", "groups" => true],
			["role", "type" => "string", "length" => "30"],
			["who", "type" => "int", "default" => "0"],
			["action", "type" => "string", "length" => "100"],
			["priv_type", "type" => "string", "length" => "30", "default" => "table"],
			["related_table", "type" => "string", "length" => "100"],
			["related_id", "type" => "int", "default" => "0"]
		);
		$this->schema->addTable("settings",
			["name", "type" => "string", "length" => "255"],
			["type", "type" => "string", "length" => "128"],
			["label", "type" => "string", "length" => "128"],
			["options", "type" => "text", "default" => ""],
			["value", "type" => "text", "default" => ""],
			["description", "type" => "text", "default" => ""],
			["category", "type" => "category", "null" => ""],
			["autoload", "type" => "bool", "default" => "0"]
		);
		$this->schema->addTable(["menus", "groups" => true],
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
		$this->schema->addTable(["queues"],
			["queue", "type" => "string"],
			["data", "type" => "text", "default" => ""],
			["position", "type" => "int", "ordered" => "queue", "default" => "0"],
			["status", "type" => "string", "default" => ""],
			["message", "type" => "text", "default" => ""]
		);

		$this->schema->addRow("users", ["email" => "root"], ["groups" => "root,admin"]);

		//admin menu
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/users"], ["content" => "Users", "icon" => "fa-users"]);
		$configuration = $this->schema->addRow("menus", ["menu" => "admin", "content" => "Configuration"], ["icon" => "fa-cogs"]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/menus"], ["parent" => $configuration, "content" => "Menus", "icon" => "fa-list"]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/emails"], ["parent" => $configuration, "content" => "Email Templates", "icon" => "fa-envelope"]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/settings"], ["parent" => $configuration, "content" => "Settings", "icon" => "fa-cog"]);

		//groups
		$this->schema->addRow("terms", ["taxonomy" => "groups", "term" => "Root"]);
		$this->schema->addRow("terms", ["taxonomy" => "groups", "term" => "User"]);
		$this->schema->addRow("terms", ["taxonomy" => "groups", "term" => "Admin"]);

		//settings categories
		$this->schema->addRow("terms", ["taxonomy" => "settings_category", "term" => "General"]);
		$this->schema->addRow("terms", ["taxonomy" => "settings_category", "term" => "SEO"]);
		$this->schema->addRow("terms", ["taxonomy" => "settings_category", "term" => "Email"]);

		//general settings
		$this->schema->addRow("settings", ["name" => "site_name"], ["category" => "settings_category general", "type" => "text", "label" => "Site Name", "autoload" => "1", "value" => "Starbug"]);
		//seo settings
		$this->schema->addRow("settings", ["name" => "meta"], ["category" => "settings_category seo", "type" => "textarea", "label" => "Custom Analytics, etc..", "autoload" => "1"]);
		$this->schema->addRow("settings", ["name" => "seo_hide"], ["category" => "settings_category seo", "type" => "checkbox", "value" => "1", "label" => "Hide from search engines", "autoload" => "1"]);
		//email settings
		$this->schema->addRow("settings", ["name" => "email_address"], ["category" => "settings_category email", "type" => "text", "label" => "Email Address"]);
		$this->schema->addRow("settings", ["name" => "email_host"], ["category" => "settings_category email", "type" => "text", "label" => "Email Host"]);
		$this->schema->addRow("settings", ["name" => "email_port"], ["category" => "settings_category email", "type" => "text", "label" => "Email Port"]);
		$this->schema->addRow("settings", ["name" => "email_username"], ["category" => "settings_category email", "type" => "text", "label" => "Email Username"]);
		$this->schema->addRow("settings", ["name" => "email_password"], ["category" => "settings_category email", "type" => "text", "label" => "Email Password"]);
		$this->schema->addRow("settings", ["name" => "email_secure"], ["category" => "settings_category email", "type" => "select", "options" => "{\"options\":\",ssl,tls\"}", "label" => "Secure SMTP"]);

		//LOGGING TABLES
		//ERROR LOG
		$this->schema->addTable(["errors"],
			["type", "type" => "string", "length" => "64"],
			["action", "type" => "string", "length" => "64", "default" => ""],
			["field", "type" => "string", "length" => "64"],
			["message", "type" => "text", "length" => "512"]
		);
		//SQL TRANSACTION LOG
		/*
			$schema->addTable("log",
				["table_name", "type" => "string", "length" => "100"],
				["object_id", "type" => "int", "default" => "0"],
				["action", "type" => "string", "length" => "16"],
				["column_name", "type" => "string", "length" => "128"],
				["old_value", "type" => "text"],
				["new_value", "type" => "text"]
			);
		*/
		$this->schema->addTable(["imports_fields"],
			["source", "type" => "text"],
			["destination", "type" => "text"],
			["update_key", "type" => "bool", "default" => "0"]
		);
		$this->schema->addTable(["imports"],
			["name", "type" => "string", "length" => "128"],
			["model", "type" => "string", "length" => "128"],
			["action", "type" => "string", "length" => "128", "default" => ""],
			["source", "type" => "int", "references" => "files id"],
			["fields", "type" => "imports_fields", "table" => "imports_fields"]
		);
	}
}
