<?php
namespace Starbug\Db\Schema;

use Starbug\Db\Query\CompilerInterface;
use Starbug\Db\Query\CompilerHookInterface;
use Starbug\Db\Query\QueryInterface;
use Starbug\Db\Query\Traits\Parsing;

class QueryCompilerHook implements CompilerHookInterface {

  use Parsing;

  public function __construct(SchemaInterface $schema) {
    $this->schema = $schema;
  }
  public function beforeCompileQuery(QueryInterface $query, CompilerInterface $compiler) {
    $this->parse($query);
  }

  protected function parse(QueryInterface $query) {
		$selection = $query->getSelection();
		foreach ($selection as $alias => $clause) {
			$query->removeSelection($alias);
			$alias = ($alias == $clause) ? false : $alias;
			$query->addSelection($this->parseColumns($query, $clause), $alias);
		}
		$condition = $query->getCondition();
		$conditions = $condition->getConditions();
		foreach ($conditions as &$c) {
			if (is_array($c)) {
				if (!empty($c["condition"])) {
					$c["condition"] = $this->parseColumns($query, $c["condition"]);
				} else if (!empty($c["field"]) && is_string($c["field"])) {
					$c["field"] = $this->parseColumn($query, $c["field"]);
				}
			}
		}
		$condition->setConditions($conditions);
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
}
