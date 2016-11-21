<?php
namespace Starbug\Core;
use Starbug\Db\Schema\HookInterface;
use Starbug\Db\Schema\SchemaInterface;
use Starbug\Db\Schema\Table;
class SchemaHook implements HookInterface {
	public function __construct(ModelFactoryInterface $models) {
		$this->models = $models;
	}
	public function addColumn($column, Table $table, SchemaInterface $schema) {
		$name = array_shift($column);
		if ($column['type'] == "category") {
			$table->set($name, "references", "terms id");
			$table->set($name, "alias", "%taxonomy% %slug%");
		} else if ($column["type"] == "path") {
			$table->set($name, "references", "aliases id");
		}
		$access_col = false;
		if ($table->getName() !== "permits" && isset($column['user_access'])) {
			$access_col = ["user_".$name];
		} else if ($table->getName() !== "permits" && isset($column['object_access'])) {
			$access_col = ["object_".$name];
		}
		if ($access_col) {
			foreach ($column as $nk => $nv) $access_col[$nk] = $nv;
			$access_col['type'] = "category";
			$access_col['null'] = "";
			$schema->addColumn("permits", $access_col);
		}
		if ($schema->hasTable($column['type']) || $this->models->has($column['type'])) {
			$ref_table_name = (empty($column['table'])) ? $table->getName()."_".$name : $column['table'];
			$schema->addTable([$ref_table_name, "groups" => false],
				["owner", "type" => "int", "null" => true, "references" => "users id", "update" => "cascade", "delete" => "cascade", "optional" => true],
				[$table->getName()."_id", "type" => "int", "default" => "NULL", "references" => $table->getName()." id", "null" => true, "update" => "cascade", "delete" => "cascade"],
				["position", "type" => "int", "ordered" => $table->getName()."_id", "optional" => true]
			);
			if ($ref_table_name != $column['type']) {
				$schema->addColumn($ref_table_name,
					[$name."_id", "type" => "int", "default" => "0", "references" => $column['type']." id", "update" => "cascade", "delete" => "cascade"]
				);
				$schema->addIndex($ref_table_name, [$table->getName()."_id", $name."_id"]);
			}
		}
	}
	public function addTable(Table $table, array $options, SchemaInterface $schema) {
		if ($table->hasOption('base') && $table->getOption('base') !== $table->getName()) {
			//find the root
			$base = $schema->getTable($table->getOption('base'));
			while ($base->hasOption('base')) $base = $schema->getTable($base->getOption("base"));
			$base = $base->getName();
			if (!$table->hasColumn($base."_id")) {
				$schema->addColumn(
					$table->getName(),
					[$base."_id", "type" => "int", "references" => $base." id"]
				);
			}
		} else {
			if (!$table->hasColumn("owner")) {
				$schema->addColumn($table->getName(),
					["owner", "type" => "int", "null" => true, "references" => "users id", "owner" => true, "optional" => true]
				);
			}
			if (!$table->hasColumn("groups") && $table->hasOption("groups") && $table->getOption("groups")) {
				$schema->addColumn($table->getName(),
					["groups", "type" => "terms", "taxonomy" => "groups", "user_access" => true, "optional" => true]
				);
			}
			if (!$table->hasColumn("statuses")) {
				$schema->addColumn($table->getName(),
					["statuses", "type" => "category", "label" => "Status", "taxonomy" => "statuses", "object_access" => true, "null" => true]
				);
			}
			if (!$table->hasColumn("created")) {
				$schema->addColumn($table->getName(),
					["created", "type" => "datetime", "default" => "0000-00-00 00:00:00", "time" => "insert"]
				);
			}
			if (!$table->hasColumn("modified")) {
				$schema->addColumn($table->getName(),
					["modified", "type" => "datetime", "default" => "0000-00-00 00:00:00", "time" => "update"]
				);
			}
		}
	}
	public function getTable(Table $table, SchemaInterface $schema) {
		$model = $table->getName();
		$columns = $table->getColumns();
		$search_cols = array_keys($columns);
		foreach ($search_cols as $colname_index => $colname_value) {
			if ($schema->hasTable($columns[$colname_value]["type"]) || $this->models->has($columns[$colname_value]["type"])) unset($search_cols[$colname_index]);
		}
		$defaults = array(
			"name" => $model,
			"label" => ucwords(str_replace(array("-", "_"), array(" ", " "), $model)),
			"singular" => rtrim($model, 's'),
			"search" => $model.'.'.implode(",$model.", $search_cols)
		);
		$defaults["singular_label"] = ucwords(str_replace(array("-", "_"), array(" ", " "), $defaults["singular"]));
		foreach ($defaults as $key => $value) {
			if (!$table->hasOption($key)) {
				$table->setOption($key, $value);
			}
		}
		foreach ($columns as $name => $column) {
			if (!$table->has($name, $column["type"])) {
				$table->set($name, $column["type"], "");
			}
			if (!$table->has($name, "label")) {
				$table->set($name, "label", ucwords(str_replace('_', ' ', $name)));
			}
		}
	}
}
?>
