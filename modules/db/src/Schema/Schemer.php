<?php
namespace Starbug\Db\Schema;
class Schemer implements SchemerInterface {
	protected $migrations = [];
	public function addMigration(MigrationInterface $migration) {
		$this->migrations[] = $migration;
		return $this;
	}
	public function migrate() {
		$this->invokeMigrations("up");
		$this->invokeMigrations("migrate");
	}
	protected function invokeMigrations($method) {
		foreach ($this->migrations as $migration) {
			call_user_func([$migration, $method]);
		}
	}
}
?>
