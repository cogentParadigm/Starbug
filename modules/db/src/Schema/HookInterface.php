<?php
namespace Starbug\Db\Schema;
interface HookInterface {
	public function addColumn($column, Table $table, SchemaInterface $schema);
	public function addTable(Table $table, array $options, SchemaInterface $schema);
	public function getTable(Table $table, SchemaInterface $schema);
}
?>
