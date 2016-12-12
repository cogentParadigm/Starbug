<?php
namespace Starbug\Files;
use Starbug\Db\Schema\AbstractMigration;
class Migration extends AbstractMigration {
	public function up() {
		$this->schema->addTable(["files", "list" => "all"],
			["filename", "type" => "string", "length" => "128"],
			["category", "type" => "category", "null" => ""],
			["mime_type", "type" => "string", "length" => "128", "display" => false],
			["size", "type" => "int", "default" => "0", "display" => false],
			["caption", "type" => "string", "length" => "255", "display" => false]
		);

		//add sortable images to content
		$this->schema->addColumn("terms", ["images", "type" => "files", "optional" => ""]);

		//files category
		$this->schema->addRow("terms", ["taxonomy" => "files_category", "term" => "Uncategorized"]);
	}
}
?>
