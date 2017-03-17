<?php
namespace Starbug\Db\Schema;
interface SchemaInterface {
	public function addHook(HookInterface $hook);
	public function addTable($table);
	public function addColumn($table);
	public function getTable($table);
	public function getTables();
	public function getEntityRoot($table);
	public function getEntityChain($table);
	public function getColumn($table, $column = "");
	public function hasTable($table);
	public function hasColumn($table, $column);
	public function dropTable($table);
	public function dropColumn($table, $column);
	public function addIndex($table, $columns);
	public function dropIndex($table, $columns);
	public function clean();
}
