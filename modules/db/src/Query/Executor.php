<?php
namespace Starbug\Db\Query;

use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\HookFactoryInterface;
use PDO;

class Executor implements ExecutorInterface {

  protected $hooks = [];

  public function __construct(DatabaseInterface $db, ModelFactoryInterface $models, HookFactoryInterface $hookFactory, CompilerInterface $compiler) {
    $this->db = $db;
    $this->models = $models;
    $this->hookFactory = $hookFactory;
    $this->compiler = $compiler;
  }

  /**
   * {@inheritDoc}
   */
  public function execute(BuilderInterface $builder) {
    $query = $builder->getQuery();
    if (!$query->isRaw() && $query->isInsert() || $query->isUpdate()) {
      if (!$query->isValidated()) $this->validate($builder, self::PHASE_VALIDATION);
      $this->validate($builder, self::PHASE_STORE);
    }

    $result = $this->compiler->build($query);

    if ($this->db->errors() && !$query->isSelect() && $query->errorsPreventSaving()) return false;

    if ($query->isDelete()) $this->validate($builder, self::PHASE_BEFORE_DELETE);
    if ($result->isExecutable()) {
      $records = $this->db->prepare($result->getSql());
      $records->execute($query->getParameters());
      if ($query->isSelect()) {
        $rows = $records->fetchAll(PDO::FETCH_ASSOC);
        return (!empty($rows) && $query->getLimit() == 1) ? $rows[0] : $rows;
      } else {
        $this->record_count = $records->rowCount();
        if ($query->isInsert()) {
          $query->setValue("id", $this->db->lastInsertId());
          $this->models->get($query->getTable()->getName())->insert_id = $this->db->lastInsertId();
        }
        if (!$query->isRaw()) {
          if ($query->isDelete()) $this->validate($builder, self::PHASE_AFTER_DELETE);
          else $this->validate($builder, self::PHASE_AFTER_STORE);
        }
        return $this->record_count;
      }
    } else {
      //only reason to get here should be an update query
      //with only 'virtual' fields, which will be very rare
      //since most tables will have a modified flag
      $this->record_count = 0;
      if (!$query->isRaw() && $query->isUpdate()) $this->validate($builder, self::PHASE_AFTER_STORE);
      return $this->record_count;
    }
  }

  /**
   * {@inheritDoc}
   */
  public function count(BuilderInterface $builder, array $params = []) {
    $query = $builder->getQuery();
    $result = $this->compiler->build($query);
    if ($this->db->errors()) return false;
    if (empty($params)) $params = $query->getParameters();
    $records = $this->db->prepare($result->getCountSql());
    $records->execute($params);
    $count = $records->fetchColumn();
    return $count;
  }

  /**
   * {@inheritDoc}
   */
  public function getConnection() {
    return $this->db;
  }

  /**
   * {@inheritDoc}
   */
  public function validate(BuilderInterface $builder, $phase = self::PHASE_VALIDATION) {
    $query = $builder->getQuery();
    if ($phase == self::PHASE_VALIDATION && !$query->isValidated()) $query->beginValidation();
    $model = $this->models->get($query->getTable()->getName());
    foreach ($model->hooks as $column => $hooks) {
      if (!isset($hooks['required']) && !isset($hooks['default']) && !isset($hooks['null']) && !isset($hooks['optional'])) $hooks['required'] = "";
      foreach ($hooks as $hook => $argument) {
        $this->invoke_hook($builder, $phase, $column, $hook, $argument);
      }
    }
    if ($phase == self::PHASE_VALIDATION) $query->setValidated(true);
  }

  protected function invoke_hook(BuilderInterface $builder, $phase, $column, $hook, $argument) {
    $query = $builder->getQuery();
    $key = false;
    $values = $query->getValues();
    $model = $query->getTable()->getName();
    $alias = $query->getAlias();
    if (isset($values[$column])) $key = $column;
    elseif (isset($values[$model.".".$column])) $key = $model.".".$column;
    elseif (isset($values[$alias.".".$column])) $key = $alias.".".$column;
    if (!isset($this->hooks["store_".$column."_".$hook])) $this->hooks["store_".$column."_".$hook] = $this->hookFactory->get("store/".$hook);
    $wasHook = $hook;
    foreach ($this->hooks["store_".$column."_".$hook] as $hook) {
      //hooks are invoked in 3 phases
      //0 = validate (before)
      //1 = store (during)
      //2 = after
      if ($phase == self::PHASE_VALIDATION) {
        if ($key == false) {
          if ($query->isInsert()) $hook->empty_before_insert($builder, $column, $argument);
          elseif ($query->isUpdate()) $hook->empty_before_update($builder, $column, $argument);
          $hook->empty_validate($builder, $column, $argument);
        } else {
          if ($query->isInsert()) $query->setValue($key, $hook->before_insert($builder, $key, $values[$key], $column, $argument));
          elseif ($query->isUpdate()) $query->setValue($key, $hook->before_update($builder, $key, $values[$key], $column, $argument));
          $query->setValue($key, $hook->validate($builder, $key, $values[$key], $column, $argument));
        }
      } elseif ($phase == self::PHASE_STORE && $key != false) {
        if ($query->isInsert()) $query->setValue($key, $hook->insert($builder, $key, $values[$key], $column, $argument));
        elseif ($query->isUpdate()) $query->setValue($key, $hook->update($builder, $key, $values[$key], $column, $argument));
        $query->setValue($key, $hook->store($builder, $key, $values[$key], $column, $argument));
      } elseif ($phase == self::PHASE_AFTER_STORE && $key != false) {
        if ($query->isInsert()) $hook->after_insert($builder, $key, $values[$key], $column, $argument);
        elseif ($query->isUpdate()) $hook->after_update($builder, $key, $values[$key], $column, $argument);
        $hook->after_store($builder, $key, $values[$key], $column, $argument);
      } elseif ($phase == self::PHASE_BEFORE_DELETE) {
        $hook->before_delete($builder, $column, $argument);
      } elseif ($phase == self::PHASE_AFTER_DELETE) {
        $hook->after_delete($builder, $column, $argument);
      }
    }
  }
}
