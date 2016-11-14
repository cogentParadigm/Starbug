<?php
namespace Starbug\Files;
use Starbug\Db\Schema\SchemaInterface;
use Starbug\Db\Schema\AbstractMigration;
class Migration extends AbstractMigration {
	public function __construct(SchemaInterface $schema) {
		$this->schema = $schema;
	}
	public function up() {
		$this->schema->addTable(["files", "list" => "all"],
			["filename", "type" => "string", "length" => "128"],
			["category", "type" => "category", "null" => ""],
			["mime_type", "type" => "string", "length" => "128", "display" => false],
			["size", "type" => "int", "default" => "0", "display" => false],
			["caption", "type" => "string", "length" => "255", "display" => false]
		);

		//add sortable images to content
		$this->schema->addColumn("uris", ["images", "type" => "files", "optional" => ""]);

		//add sortable images to content
		$this->schema->addColumn("terms", ["images", "type" => "files", "optional" => ""]);

		//files category
		/*
		$this->taxonomy("files_category",
			["term" => "Uncategorized"]
		);
		*/
	}
}
?>
