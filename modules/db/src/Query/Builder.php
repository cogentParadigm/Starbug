<?php
namespace Starbug\Db\Query;

use Starbug\Db\Schema\Schema;

class Builder implements BuilderInterface {

  use Traits\Hooks;
  use Traits\Parsing;
  use Traits\Builder;
  use Traits\Pagination;
  use Traits\Execution;

  protected $schema;

  public function __construct(ExecutorInterface $executor) {
    $this->executor = $executor;
    $this->reset();
  }

  public function setSchema(Schema $schema) {
    $this->schema = $schema;
  }

  public function subquery($field, $callable = false) {
    //split the column into parts
    $parts = explode(".", $field);
    //the first token is either a table alias or the name of a column that references another table
    //if it's a column, then we'll assume it's a column of our base table
    $alias = $this->query->getAlias();
    //if it's a collection, we'll use it
    if ($this->query->hasTable($parts[0])) {
      $alias = array_shift($parts);
    }
    $token = array_shift($parts);
    $parsed = $this->parseName($token);
    $table = $this->query->getTable($alias)->getName();
    $schema = $this->schema->getColumn($table, $parsed["name"]);
    $query = $this->query();
    $nextAlias = $alias;
    if (!empty($schema["references"])) {
      $ref = explode(" ", $schema["references"]);
      $query->from($ref[0])->where($ref[0].'.'.$ref[1].'='.$alias.'.'.$token);
      $nextAlias = $ref[0];
    } elseif ($this->schema->hasTable($schema["type"])) {
      if (empty($schema["table"])) $schema["table"] = $schema["entity"]."_".$parsed["name"];
      $query->from($schema["table"])->where($schema["table"].".".$schema["entity"]."_id=".$alias.".id");
      $nextAlias = $schema["table"];
      if ($schema["table"] != $schema["type"]) {
        $query->joinOne($schema["table"].".".$token."_id", $schema["type"]);
        $nextAlias = $schema["type"];
      }
    }
    if (!empty($parts)) {
      $query->select($nextAlias.".".implode(".", $parts));
    }
    if ($callable) call_user_func($callable, $query, $this);
    return $this;
  }

  public function query($table = false) {
    $builder = new static($this->executor);
    $builder->setSchema($this->schema);
    if ($table) $builder->from($table);
    return $builder;
  }

}
