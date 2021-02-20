<?php
namespace Starbug\Core;

use Starbug\Db\Schema\HookInterface;
use Starbug\Db\Schema\SchemaInterface;
use Starbug\Db\Schema\Table as SchemaTable;

class SchemaHook implements HookInterface {
  public function addColumn($column, SchemaTable $table, SchemaInterface $schema) {
    $name = array_shift($column);
    if ($column['type'] == "category") {
      $table->set($name, "references", "terms id");
      $table->set($name, "alias", "%taxonomy% %slug%");
    } elseif ($column["type"] == "path") {
      $table->set($name, "references", "aliases id");
    }
    $access_col = false;
    if ($table->getName() !== "permits" && isset($column['user_access'])) {
      $access_col = ["user_".$name];
    } elseif ($table->getName() !== "permits" && isset($column['object_access'])) {
      $access_col = ["object_".$name];
    }
    if ($access_col) {
      foreach ($column as $nk => $nv) {
        $access_col[$nk] = $nv;
      }
      if ($schema->hasTable($column['type'])) {
        $access_col['type'] = "int";
        $access_col['references'] = $column["type"]." id";
      } else {
        $access_col['type'] = $column["type"];
      }
      $access_col['null'] = true;
      $access_col["default"] = "NULL";
      $schema->addColumn("permits", $access_col);
    }
    if ($schema->hasTable($column['type'])) {
      $ref_table_name = (empty($column['table'])) ? $table->getName()."_".$name : $column['table'];
      $schema->addTable([$ref_table_name, "groups" => false],
        ["owner", "type" => "int", "null" => true, "references" => "users id", "owner" => true, "update" => "cascade", "delete" => "cascade", "optional" => true],
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
  public function addTable(SchemaTable $table, array $options, SchemaInterface $schema) {
    if ($table->hasOption('base') && $table->getOption('base') !== $table->getName()) {
      // find the root
      $base = $schema->getTable($table->getOption('base'));
      while ($base->hasOption('base')) {
        $base = $schema->getTable($base->getOption("base"));
      }
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
      if (!$table->hasColumn("deleted")) {
        $schema->addColumn($table->getName(),
          ["deleted", "type" => "bool", "default" => "0", "object_access" => true]
        );
      }
      if (!$table->hasColumn("created")) {
        $schema->addColumn($table->getName(),
          ["created", "type" => "datetime", "default" => "0000-00-00 00:00:00", "timestamp" => "insert"]
        );
      }
      if (!$table->hasColumn("modified")) {
        $schema->addColumn($table->getName(),
          ["modified", "type" => "datetime", "default" => "0000-00-00 00:00:00", "timestamp" => "update"]
        );
      }
    }
  }
  public function getTable(SchemaTable $table, SchemaInterface $schema) {
    $model = $table->getName();
    $columns = $table->getColumns();
    $search_cols = array_keys($columns);
    foreach ($search_cols as $colname_index => $colname_value) {
      if ($schema->hasTable($columns[$colname_value]["type"])) {
        unset($search_cols[$colname_index]);
      }
    }
    $defaults = [
      "name" => $model,
      "label" => ucwords(str_replace(["-", "_"], [" ", " "], $model)),
      "singular" => rtrim($model, 's'),
      "search_fields" => $model.'.'.implode(",$model.", $search_cols)
    ];
    $defaults["singular_label"] = ucwords(str_replace(["-", "_"], [" ", " "], $defaults["singular"]));
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
