<?php
namespace Starbug\Db\Schema;
class Table {
	protected $schema;
	protected $name;
	protected $columns = array();
	protected $options = array();
	protected $indexes = array();
	protected $triggers = array();
	protected $dropped = false;
	public function __construct(SchemaInterface $schema, $name) {
		$this->schema = $schema;
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function hasColumn($column) {
		return isset($this->columns[$column]);
	}
	public function addColumn($column) {
		$args = func_get_args();
		foreach ($args as $column) {
			$name = array_shift($column);
			$this->columns[$name] = $column + ["dropped" => false, "entity" => $this->name];
		}
	}
	public function get($column, $key) {
		return $this->columns[$column][$key];
	}
	public function set($column, $key, $value) {
		$this->columns[$column][$key] = $value;
	}
	public function has($column, $key) {
		return isset($this->columns[$column][$key]);
	}
	public function dropColumn($name) {
		$this->columns[$name]["dropped"] = true;
	}
	public function getColumns() {
		$columns = $this->columns;
		$primary = [];
		foreach ($columns as $column => $options) {
			if ((isset($options['key'])) && ("primary" == $options['key'])) $primary[] = $column;
		}
		if (empty($primary)) $columns['id'] = ["type" => "int", "auto_increment" => true, "key" => "primary"];
		if ($this->hasOption("base") && $this->getOption("base") !== $this->name) {
			$columns = array_merge($this->schema->getTable($this->getOption("base"))->getColumns(), $columns);
		}
		return $columns;
	}
	public function setOption($name, $value) {
		$this->options[$name] = $value;
	}
	public function getOption($name) {
		return $this->options[$name];
	}
	public function getOptions() {
		return $this->options;
	}
	public function hasOption($name) {
		return isset($this->options[$name]);
	}
	function addIndex($columns) {
		$key = implode("_", $columns);
		$this->indexes[$key]["columns"] = $columns;
		$this->indexes[$key]["dropped"] = false;
	}
	function dropIndex($columns) {
		$key = implode("_", $columns);
		$this->indexes[$key]["dropped"] = true;
	}
	function getIndexes() {
		return $this->indexes;
	}
	public function drop() {
		$this->dropped = true;
	}
	public function restore() {
		$this->dropped = false;
	}
	public function isDropped() {
		return $this->dropped;
	}
}
