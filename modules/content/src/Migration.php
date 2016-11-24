<?php
namespace Starbug\Content;
use Starbug\Db\Schema\AbstractMigration;
class Migration extends AbstractMigration {
	public function up() {
		$this->schema->addTable(["aliases", "singular" => "alias", "label_select" => "aliases.path"],
			["path", "type" => "string", "length" => 255, "index" => true],
			["alias", "type" => "string", "length" => 255, "index" => true, "unique" => true]
		);
		$this->schema->addTable(["categories", "label_select" => "categories.title"],
			["title", "type" => "string", "length" => "128"],
			["path", "type" => "path", "path" => "categories/view/[categories:id]", "pattern" => "category/[categories:title]", "null" => true, "default" => "NULL"],
			["description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
			["parent", "type" => "int", "default" => "0", "materialized_path" => "tree_path"],
			["position", "type" => "int", "ordered" => "parent"],
			["tree_path", "type" => "string", "length" => "255", "default" => ""]
		);
		$this->schema->addTable(["tags", "label_select" => "tags.title"],
			["title", "type" => "string", "length" => "128"],
			["path", "type" => "path", "path" => "tags/view/[tags:id]", "pattern" => "tag/[tags:title]", "null" => true, "default" => "NULL"],
			["description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => ""],
			["parent", "type" => "int", "default" => "0", "materialized_path" => "tree_path"],
			["position", "type" => "int", "ordered" => "parent"],
			["tree_path", "type" => "string", "length" => "255", "default" => ""]
		);
		$this->schema->addTable(["blocks"],
			["region", "type" => "string", "length" => "64", "default" => "content"],
			["type", "type" => "string", "length" => "32", "default" => "text"],
			["content", "type" => "text", "default" => ""]
		);
		$this->schema->addTable(["pages", "label_select" => "pages.title", "groups" => true],
			["title", "type" => "string", "length" => "128"],
			["path", "type" => "path", "path" => "pages/view/[pages:id]", "pattern" => "page/[pages:title]", "null" => true, "default" => "NULL"],
			["template", "type" => "string", "length" => "64", "default" => ""],
			["categories", "type" => "categories", "optional" => ""],
			["tags", "type" => "tags", "column" => "title"],
			["parent", "type" => "int", "default" => "0"],
			["type", "type" => "string", "default" => ""],
			["theme", "type" => "string", "length" => "128", "default" => ""],
			["layout", "type" => "string", "length" => "64", "default" => ""],
			["description", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => "", "list" => "false"],
			["meta", "type" => "text", "default" => "", "list" => "false"],
			["meta_keywords", "type" => "string", "length" => "255", "input_type" => "textarea", "default" => "", "list" => "false"],
			["canonical", "type" => "string", "length" => "255", "default" => "", "list" => "false"],
			["breadcrumb", "type" => "string", "length" => "255", "default" => "", "list" => "false"],
			["blocks", "type" => "blocks", "table" => "blocks"],
			["images", "type" => "files", "optional" => true],
			["published", "type" => "bool", "default" => "0"]
		);
		$this->schema->addColumn("menus", ["pages_id", "type" => "int", "references" => "pages id", "label" => "Page", "null" => "", "default" => "NULL", "update" => "cascade", "delete" => "cascade"]);

		// CONTENT TYPES
		//$this->schema->addTable(["posts", "base" => "pages", "description" => "A blog post"]);

		//admin menu
		$content = $this->schema->addRow("menus", ["menu" => "admin", "content" => "Content"]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/pages", "content" => "Pages"], ["parent" => $content]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/categories", "content" => "Categories"], ["parent" => $content]);
		$this->schema->addRow("menus", ["menu" => "admin", "href" => "admin/tags", "content" => "Tags"], ["parent" => $content]);

		//categories
		$this->schema->addRow("categories", ["title" => "Uncategorized"]);

		//tags
		$this->schema->addRow("tags", ["title" => "Uncategorized"]);
	}
}
?>
