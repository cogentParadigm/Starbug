<?php
namespace Starbug\Db\Query;

use Starbug\Core\DatabaseInterface;
use Starbug\Core\ModelFactoryInterface;
use Starbug\Core\HookFactoryInterface;
use PDO;

class Executor implements ExecutorInterface {

  protected $hooks = [];

  public function __construct(ModelFactoryInterface $models, HookFactoryInterface $hookFactory, CompilerInterface $compiler) {
    $this->models = $models;
    $this->hookFactory = $hookFactory;
    $this->compiler = $compiler;
  }

  /**
   * {@inheritDoc}
   */
  public function execute(BuilderInterface $builder) {
    $db = $builder->getDatabase();
    $query = $builder->getQuery();
    if (!$query->isRaw() && $query->isInsert() || $query->isUpdate()) {
      if (!$query->isValidated()) $this->validate($builder, self::PHASE_VALIDATION);
      $this->validate($builder, self::PHASE_STORE);
    }

    $result = $this->compiler->build($query);

    if ($db->errors() && !$query->isSelect() && $query->errorsPreventSaving()) return false;

    if ($query->isDelete()) $this->validate($builder, self::PHASE_BEFORE_DELETE);
    if ($result->isExecutable()) {
      $records = $db->prepare($result->getSql());
      $records->execute($query->getParameters());
      if ($query->isSelect()) {
        $rows = $records->fetchAll(PDO::FETCH_ASSOC);
        return (!empty($rows) && $query->getLimit() == 1) ? $rows[0] : $rows;
      } else {
        $this->record_count = $records->rowCount();
        if ($query->isInsert()) {
          $query->setValue("id", $db->lastInsertId());
          $this->models->get($query->getTable()->getName())->insert_id = $db->lastInsertId();
        }
        if (!$query->isRaw()) {
          if ($query->isDelete()) $this->validate($builder, self::PHASE_AFTER_DELETE);
          else $this->validate($builder, self::PHASE_AFTER_STORE);
        }
        return $this->record_count;
      }
    } else {
      // only reason to get here should be an update query
      // with only 'virtual' fields, which will be very rare
      // since most tables will have a modified flag
      $this->record_count = 0;
      if (!$query->isRaw() && $query->isUpdate()) $this->validate($builder, self::PHASE_AFTER_STORE);
      return $this->record_count;
    }
  }

  /**
   * {@inheritDoc}
   */
  public function count(BuilderInterface $builder, array $params = []) {
    $db = $builder->getDatabase();
    $query = $builder->getQuery();
    $result = $this->compiler->build($query);
    if ($db->errors()) return false;
    if (empty($params)) $params = $query->getParameters();
    $records = $db->prepare($result->getCountSql());
    $records->execute($params);
    $count = $records->fetchColumn();
    return $count;
  }

  /**
   * {@inheritDoc}
   */
  public function validate(BuilderInterface $builder, $phase = self::PHASE_VALIDATION) {
    $query = $builder->getQuery();
    if ($phase == self::PHASE_VALIDATION && !$query->isValidated()) $query->beginValidation();
    $tableName = $query->getTable()->getName();
    if ($this->models->has($tableName)) {
      $model = $this->models->get($tableName);
      foreach ($model->hooks as $column => $hooks) {
        if (!isset($hooks['required']) && !isset($hooks['default']) && !isset($hooks['null']) && !isset($hooks['optional'])) $hooks['required'] = "";
        foreach ($hooks as $hook => $argument) {
          $this->invokeHook($builder, $phase, $column, $hook, $argument);
        }
      }
    }
    if ($phase == self::PHASE_VALIDATION) $query->setValidated(true);
  }

  /**
   * Replaces any parameter placeholders in a query with the value of that
   * parameter. Useful for debugging. Assumes anonymous parameters from
   * $params are are in the same order as specified in $query
   *
   * @param string $query The sql query with parameter placeholders
   * @param array $params The array of substitution parameters
   *
   * @return string The interpolated query
   */
  public function interpolate(QueryInterface $query, $params = null) {
    $result = $this->compiler->build($query);
    if (is_null($params)) $params = $query->getParameters();
    $keys = [];
    $values = $params;

    // build a regular expression for each parameter
    foreach ($params as $key => $value) {
      if (is_string($key)) {
        $keys[] = '/'.$key.'/';
      } else {
        $keys[] = '/[?]/';
      }

      if (is_array($value)) $values[$key] = implode(',', $value);

      if (is_null($value)) $values[$key] = 'NULL';
    }
    // Walk the array to see if we can add single-quotes to strings
    array_walk($values, create_function('&$v, $k', 'if (!is_numeric($v) && $v!="NULL") $v = "\'".$v."\'";'));

    $interpolation = preg_replace($keys, $values, $result->getSql());

    return $interpolation;
  }

  protected function invokeHook(BuilderInterface $builder, $phase, $column, $hook, $argument) {
    $query = $builder->getQuery();
    $key = false;
    $model = $query->getTable()->getName();
    $alias = $query->getAlias();
    if ($query->hasValue($column)) $key = $column;
    elseif ($query->hasValue($model.".".$column)) $key = $model.".".$column;
    elseif ($query->hasValue($alias.".".$column)) $key = $alias.".".$column;
    if (!isset($this->hooks["store_".$column."_".$hook])) $this->hooks["store_".$column."_".$hook] = $this->hookFactory->get("store/".$hook);
    $wasHook = $hook;
    foreach ($this->hooks["store_".$column."_".$hook] as $hook) {
      // hooks are invoked in 3 phases
      // 0 = validate (before)
      // 1 = store (during)
      // 2 = after
      if ($phase == self::PHASE_VALIDATION) {
        if ($key == false) {
          if ($query->isInsert()) $hook->emptyBeforeInsert($builder, $column, $argument);
          elseif ($query->isUpdate()) $hook->emptyBeforeUpdate($builder, $column, $argument);
          $hook->emptyValidate($builder, $column, $argument);
        } else {
          if ($query->isInsert()) $query->setValue($key, $hook->beforeInsert($builder, $key, $query->getValue($key), $column, $argument));
          elseif ($query->isUpdate()) $query->setValue($key, $hook->beforeUpdate($builder, $key, $query->getValue($key), $column, $argument));
          $query->setValue($key, $hook->validate($builder, $key, $query->getValue($key), $column, $argument));
        }
      } elseif ($phase == self::PHASE_STORE && $key != false) {
        if ($query->isInsert()) $query->setValue($key, $hook->insert($builder, $key, $query->getValue($key), $column, $argument));
        elseif ($query->isUpdate()) $query->setValue($key, $hook->update($builder, $key, $query->getValue($key), $column, $argument));
        $query->setValue($key, $hook->store($builder, $key, $query->getValue($key), $column, $argument));
      } elseif ($phase == self::PHASE_AFTER_STORE && $key != false) {
        if ($query->isInsert()) $hook->afterInsert($builder, $key, $query->getValue($key), $column, $argument);
        elseif ($query->isUpdate()) $hook->afterUpdate($builder, $key, $query->getValue($key), $column, $argument);
        $hook->afterStore($builder, $key, $query->getValue($key), $column, $argument);
      } elseif ($phase == self::PHASE_BEFORE_DELETE) {
        $hook->beforeDelete($builder, $column, $argument);
      } elseif ($phase == self::PHASE_AFTER_DELETE) {
        $hook->afterDelete($builder, $column, $argument);
      }
    }
  }
}
