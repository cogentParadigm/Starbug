<?php
namespace Starbug\Db\Schema;
class AbstractMigration implements MigrationInterface {
	public function __construct(SchemaInterface $schema) {
		$this->schema = $schema;
	}
	public function up() {

	}
	public function down() {

	}
	public function migrate() {
		
	}
}
?>
