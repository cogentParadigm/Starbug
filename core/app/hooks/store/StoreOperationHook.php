<?php
namespace Starbug\Core;

use Starbug\Db\Schema\SchemaInterface;

class StoreOperationHook extends QueryHook {
  protected $replace = false;
  public function __construct(ModelFactoryInterface $models, DatabaseInterface $db, SchemaInterface $schema) {
    $this->models = $models;
    $this->db = $db;
    $this->schema = $schema;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (is_array($value)) {
      $hooks = $this->schema->getColumn($query->model, $column);
      if ($this->schema->hasTable($hooks["type"])) {
        $model = $hooks["type"];
      } else {
        $model = explode(" ", $hooks["references"])[0];
      }
      $instance = $this->models->get($model);
      $instance->$argument($value);
      if (!$instance->errors()) {
        $value = empty($value["id"]) ? $this->db->getInsertId($model) : $value["id"];
      } else {
        $this->db->errors->set($query->model, $key, $this->db->errors->get($model));
        $this->db->errors->set($model, null);
      }
    }
    return $value;
  }
}
