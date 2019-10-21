<?php
namespace Starbug\Db\Schema;

use Starbug\Db\Query\CompilerInterface;
use Starbug\Db\Query\CompilerHookInterface;
use Starbug\Db\Query\QueryInterface;
use Starbug\Db\Query\Traits\Parsing;
use Starbug\Db\Query\ConditionInterface;

class QueryCompilerHook implements CompilerHookInterface {

  use Parsing;

  public function __construct(SchemaInterface $schema) {
    $this->schema = $schema;
  }
  public function beforeCompileQuery(QueryInterface $query, CompilerInterface $compiler) {
    $this->parse($query);
  }

  protected function parse(QueryInterface $query) {
    if ($query->hasTag("Starbug\Db\Schema\QueryCompilerHook")) {
      return;
    }
    $query->addTag("Starbug\Db\Schema\QueryCompilerHook");
    $selection = $query->getSelection();
    foreach ($selection as $alias => $clause) {
      if (is_string($clause)) {
        $query->removeSelection($alias);
        $alias = ($alias == $clause) ? false : $alias;
        $query->addSelection($this->parseColumns($query, $clause), $alias);
      }
    }
    $condition = $query->getCondition();
    $conditions = $condition->getConditions();
    $this->parseCondition($query, $condition);
    $groups = $query->getGroup();
    $query->setGroup([]);
    foreach ($groups as $group => $value) {
      $query->addGroup($this->parseColumns($query, $group));
    }
    $values = $query->getValues();
    $query->setValues([]);
    foreach ($values as $key => $value) {
      $query->setValue($this->parseColumn($query, $key), $value);
    }
  }

  protected function parseCondition(QueryInterface $query, ConditionInterface $condition) {
    $conditions = $condition->getConditions();
    foreach ($conditions as &$c) {
      if (!empty($c["condition"])) {
        if ($c["condition"] instanceof ConditionInterface) {
          $this->parseCondition($query, $c["condition"]);
        } else {
          $c["condition"] = $this->parseColumns($query, $c["condition"]);
        }
      } elseif (!empty($c["field"]) && is_string($c["field"])) {
        $c["field"] = $this->parseColumn($query, $c["field"]);
      }
    }
    $condition->setConditions($conditions);
  }
}
