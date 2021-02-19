<?php
namespace Starbug\Content;

use Starbug\Core\QueryHook;
use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\MacroInterface;
use Starbug\Core\InputFilterInterface;
use Starbug\Db\Schema\SchemaInterface;

class StorePathHook extends QueryHook {
  public function __construct(DatabaseInterface $db, SchemaInterface $schema, MacroInterface $macro, InputFilterInterface $filter) {
    $this->db = $db;
    $this->macro = $macro;
    $this->schema = $schema;
    $this->filter = $filter;
  }
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, $this->beforeInsert($query, $column, "", $column, $argument));
  }
  public function beforeInsert($query, $key, $value, $column, $argument) {
    if (empty($value)) {
      $value = $this->generate($query, $column);
    }
    if (!is_numeric($value)) $query->exclude($key);
    return $value;
  }
  public function beforeUpdate($query, $key, $value, $column, $argument) {
    $path = $this->macro->replace($argument, [$query->model => ["id" => $query->getId()]]);
    if (empty($value)) {
      $value = $this->generate($query, $column, $path);
    }
    if (!is_numeric($value)) {
      $value = $this->save($value, $path);
    }
    return $value;
  }
  public function afterInsert($query, $key, $value, $column, $argument) {
    $id = $query->getId();
    $path = $this->macro->replace($argument, [$query->model => ["id" => $id]]);
    if (!is_numeric($value)) {
      // value is not an ID, so we take it as a new path
      $value = $this->save($value, $path);
      $this->db->store($query->model, ["id" => $id, $key => $value]);
    }
    return $value;
  }

  public function generate($query, $column, $path = false) {
    $pattern = $this->schema->getColumn($query->model, $column)["pattern"];
    $data = [$query->model => $query->getValues()];
    $value = $this->macro->replace($pattern, $data);
    $value = strtolower(str_replace(" ", "-", $this->filter->normalize($value, 'a-zA-Z0-9 \-_\/')));

    if (false !== $path) {
      $path = $this->macro->replace($path, $data);
    }

    $base = $value;
    $exists = $this->exists($value, $path);
    $count = 1;
    while ($exists->one()) {
      $value = $base."-".$count;
      $exists = $this->exists($value, $path);
      $count++;
    }
    return $value;
  }

  public function save($value, $path) {
    if ($exists = $this->exists(["path" => $path])->one()) {
      $this->db->store("aliases", ["id" => $exists["id"], "alias" => $value]);
      return $exists["id"];
    } else {
      $this->db->store("aliases", ["alias" => $value, "path" => $path]);
      return $this->db->getInsertId("aliases");
    }
  }

  public function exists($alias, $path = false) {
    $exists = $this->db->query("aliases");
    if (!is_array($alias)) {
      $alias = ["alias" => $alias];
    }
    $exists->conditions($alias);
    if (false !== $path) {
      $exists->condition("aliases.path", $path, "!=");
    }
    return $exists;
  }
}
