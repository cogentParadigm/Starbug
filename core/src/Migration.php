<?php
namespace Starbug\Core;
use Starbug\Db\Schema\SchemaInterface;
use Starbug\Db\Schema\AbstractMigration;
class Migration extends AbstractMigration {
	public function __construct(SchemaInterface $schema) {
		$this->schema = $schema;
	}
	public function up() {
		// This adds a table to the schema, The Schemer builds up a schema with all of the migrations that are to be run, and then updates the db
		$this->schema->addTable(["users", "label_select" => "CONCAT(first_name, ' ', last_name, ' (', email, ')')", "groups" => true],
			["first_name", "type" => "string", "length" => "64", "list" => "true"],
			["last_name", "type" => "string", "length" => "64", "list" => "true"],
			["email", "type" => "string", "length" => "128", "unique" => "", "list" => "true"],
			["password", "type" => "password", "confirm" => "password_confirm", "optional_update" => ""],
			["last_visit", "type" => "datetime", "default" => "0000-00-00 00:00:00", "list" => "true", "display" => "false"]
		);
		//This will be stored immediately after the creation of the users table
		$this->schema->addRow("users", ["email" => "root"], ["groups" => "root,admin"]);
		$this->schema->addTable(["permits", "list" => "all", "groups" => true],
			["role", "type" => "string", "length" => "30"],
			["who", "type" => "int", "default" => "0"],
			["action", "type" => "string", "length" => "100"],
			["priv_type", "type" => "string", "length" => "30", "default" => "table"],
			["related_table", "type" => "string", "length" => "100"],
			["related_id", "type" => "int", "default" => "0"]
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
		$this->schema->addTable(["alias"],
			["path", "type" => "string", "length" => 255, "index" => true],
			["alias", "type" => "string", "length" => 255, "index" => true]
		);
		$this->schema->addTable(["uris", "label" => "Pages", "singular_label" => "Page", "label_select" => "title", "groups" => true],
			["title", "type" => "string", "length" => "128", "list" => "true"],
			["path", "type" => "string", "length" => "64", "unique" => "", "list" => "true", "slug" => "title", "null" => "", "pattern" => "[path:token]"],
			["template", "type" => "string", "length" => "64", "default" => "", "list" => "false"],
			["categories", "type" => "terms", "optional" => ""],
			["tags", "type" => "terms", "column" => "term"],
			["parent", "type" => "int", "default" => "0", "list" => "false"],
			["type", "type" => "string", "default" => "", "list" => "false"],
			["theme", "type" => "string", "length" => "128", "default" => "", "list" => "false"],
			["layout", "type" => "string", "length" => "64", "default" => ""],
			["description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => "", "list" => "false"],
			["meta", "type" => "text", "default" => "", "list" => "false"],
			["meta_keywords", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => "", "list" => "false"],
			["canonical", "type" => "string", "length" => "255", "default" => "", "list" => "false"],
			["breadcrumb", "type" => "string", "length" => "255", "default" => "", "list" => "false"],
			["controller", "type" => "string", "default" => ""],
			["action", "type" => "string", "default" => ""]
		);
		$this->schema->addTable(["entities"],
			["base", "type" => "string", "default" => ""],
			["name", "type" => "string", "length" => "128"],
			["label", "type" => "string", "length" => "128"],
			["singular", "type" => "string", "length" => "128"],
			["singular_label", "type" => "string", "length" => "128"],
			["url_pattern", "type" => "string"],
			["description", "type" => "string", "length" => "255", "default" => ""]
		);
		$this->schema->addTable(["blocks", "list" => "all"],
			["uris_id", "type" => "int", "references" => "uris id", "alias" => "%path%"],
			["region", "type" => "string", "length" => "64", "default" => "content"],
			["type", "type" => "string", "length" => "32", "default" => "text"],
			["content", "type" => "text", "default" => ""],
			["position", "type" => "int", "ordered" => "uris_id"]
		);
		$this->schema->addTable("uris", ["blocks", "type" => "blocks", "table" => "blocks"]);
		$this->schema->addTable(["menus", "groups" => true],
			["menu", "type" => "string", "length" => "32", "list" => "true", "display" => "false"],
			["parent", "type" => "int", "default" => "0", "materialized_path" => "menu_path"],
			["uris_id", "type" => "int", "references" => "uris id", "label" => "Page", "null" => "", "default" => "NULL", "update" => "cascade", "delete" => "cascade"],
			["href", "type" => "string", "length" => "255", "label" => "URL", "default" => ""],
			["content", "type" => "string", "length" => "255", "default" => ""],
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
		// CONTENT TYPES
		$this->schema->addTable(["views", "base" => "uris", "description" => "A basic view"]);
		$this->schema->addTable(["pages", "base" => "uris", "description" => "A basic page"]);

		// URIS
		$this->schema->addRow("uris", ["path" => "api"], ["controller" => "apiRouting", "action" => "response", "statuses" => "published"]);
		$this->schema->addRow("uris", ["path" => "profile"], ["controller" => "profile", "statuses" => "published"]);
		//Admin
		$this->schema->addRow("uris", ["path" => "admin"], ["controller" => "admin", "action" => "default_action", "groups" => "admin", "theme" => "storm", "statuses" => "published"]);
		//Uploader
		$this->schema->addRow("uris", ["path" => "upload"], ["controller" => "upload", "template" => "xhr", "groups" => "user", "statuses" => "published"]);
		//terms
		$this->schema->addRow("uris", ["path" => "terms"], ["template" => "xhr", "groups" => "user", "statuses" => "published"]);
		$this->schema->addRow("uris", ["path" => "robots"], ["template" => "txt", "statuses" => "published"]);

		//admin menu
		$content = $this->schema->addRow("menus", ["menu" => "admin", "content" => "Content"]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/views", "content" => "Views"], ["parent" => $content]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/pages", "content" => "Pages"], ["parent" => $content]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/users", "content" => "Users"]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/media", "content" => "Media", "target" => "_blank"]);
		$configuration = $this->schema->addRow("menus", ["menu" => "admin", "content" => "Configuration"]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/taxonomies", "content" => "Taxonomy"], ["parent" => $configuration]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/menus", "content" => "Menus"], ["parent" => $configuration]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/emails", "content" => "Email Templates"], ["parent" => $configuration]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/settings", "content" => "Settings"], ["parent" => $configuration]);

		//groups
		$this->schema->addRow("terms", ["taxonomy" => "groups", "term" => "Root"]);
		$this->schema->addRow("terms", ["taxonomy" => "groups", "term" => "User"]);
		$this->schema->addRow("terms", ["taxonomy" => "groups", "term" => "Admin"]);

		//statuses
		$this->schema->addRow("terms", ["taxonomy" => "statuses", "term" => "Deleted"]);
		$this->schema->addRow("terms", ["taxonomy" => "statuses", "term" => "Pending"]);
		$this->schema->addRow("terms", ["taxonomy" => "statuses", "term" => "Published"]);
		$this->schema->addRow("terms", ["taxonomy" => "statuses", "term" => "Private"]);

		//uris categories
		$this->schema->addRow("terms", ["taxonomy" => "uris_categories", "term" => "Uncategorized"]);

		//uris tags
		$this->schema->addRow("terms", ["taxonomy" => "uris_tags", "term" => "Uncategorized"]);

		//settings categories
		$this->schema->addRow("terms", ["taxonomy" => "settings_category", "term" => "General"]);
		$this->schema->addRow("terms", ["taxonomy" => "settings_category", "term" => "SEO"]);
		$this->schema->addRow("terms", ["taxonomy" => "settings_category", "term" => "Themes"]);
		$this->schema->addRow("terms", ["taxonomy" => "settings_category", "term" => "Email"]);

		//general settings
		$this->schema->addRow("settings", ["name" => "site_name"], ["category" => "settings_category general", "type" => "text", "label" => "Site Name", "autoload" => "1", "value" => "Starbug"]);
		$this->schema->addRow("settings", ["name" => "tagline"], ["category" => "settings_category general", "type" => "text", "label" => "Tagline", "autoload" => "1", "value" => "Fresh XHTML and CSS, just like mom used to serve!"]);
		$this->schema->addRow("settings", ["name" => "default_path"], ["category" => "settings_category general", "type" => "text", "label" => "Default Path", "autoload" => "1", "value" => "home"]);
		//seo settings
		$this->schema->addRow("settings", ["name" => "meta"], ["category" => "settings_category seo", "type" => "textarea", "label" => "Custom Analytics, etc..", "autoload" => "1"]);
		$this->schema->addRow("settings", ["name" => "seo_hide"], ["category" => "settings_category seo", "type" => "checkbox", "value" => "1", "label" => "Hide from search engines", "autoload" => "1"]);
		//theme settings
		$this->schema->addRow("settings", ["name" => "theme"], ["category" => "settings_category themes", "type" => "text", "label" => "Theme", "autoload" => "1", "value" => "starbug-1"]);
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
		$this->schema->addTable(["logs"],
			["channel", "type" => "string"],
			["level", "type" => "int"],
			["message", "type" => "text", "default" => ""],
			["time", "type" => "int"]
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
?>
