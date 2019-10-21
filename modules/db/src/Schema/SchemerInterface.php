<?php
namespace Starbug\Db\Schema;

interface SchemerInterface {
  public function addMigration(MigrationInterface $migration);
  public function up();
  public function migrate();
  public function getSchema();
}
