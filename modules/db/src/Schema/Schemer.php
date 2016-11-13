<?php
namespace Starbug\Db\Schema;
class Schemer implements SchemerInterface {
	protected $isUp = false;
	protected $migrations = [];
	public function __construct(SchemaInterface $schema) {
		$this->schema = $schema;
	}
	public function addMigration(MigrationInterface $migration) {
		$this->migrations[] = $migration;
		return $this;
	}
	public function up() {
		if (!$this->isUp) {
			$this->invokeMigrations("up");
			$this->isUp = true;
		}
	}
	public function migrate() {
		$this->up();
		$this->invokeMigrations("migrate");
	}
	public function getSchema() {
		$this->up();
		return $this->schema;
	}
	protected function invokeMigrations($method) {
		foreach ($this->migrations as $migration) {
			call_user_func([$migration, $method]);
		}
	}
}
?>
