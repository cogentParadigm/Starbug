<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;
use Starbug\Db\Schema\SchemaInterface;
use Starbug\Operation\OperationFactoryInterface;

class StoreOperationHook extends ExecutorHook {
  public function __construct(
    protected DatabaseInterface $db,
    protected SchemaInterface $schema,
    protected OperationFactoryInterface $operations
  ) {
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (is_array($value)) {
      $hooks = $this->schema->getColumn($query->model, $column);
      if ($this->schema->hasTable($hooks["type"])) {
        $model = $hooks["type"];
      } else {
        $model = explode(" ", $hooks["references"])[0];
      }

      $operation = $this->operations->get($argument);
      $operation->configure(["model" => $model]);
      $operation->execute($value);
      if ($operation->success()) {
        $value = empty($value["id"]) ? $this->db->getInsertId($model) : $value["id"];
      } else {
        $this->db->errors->set($query->model, $key, $this->db->errors->get($model));
        $this->db->errors->set($model, null);
      }
    }
    return $value;
  }
}
