<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\DatabaseInterface;
use Starbug\Db\Query\ExecutorHook;
use Starbug\Db\Schema\SchemaInterface;

class StoreAliasHook extends ExecutorHook {
  protected $db;
  protected $schema;
  public function __construct(DatabaseInterface $db, SchemaInterface $schema) {
    $this->db = $db;
    $this->schema = $schema;
  }
  public function validate($query, $key, $value, $column, $alias) {
    if (!empty($value) && !is_numeric($value) && $value != "NULL") {
      $hooks = $this->schema->getColumn($query->model, $column);
      if ($this->schema->hasTable($hooks["type"])) {
        $referenced_model = [$hooks["type"], "id"];
      } else {
        $referenced_model = explode(" ", $hooks["references"]);
      }
      // $alias might be '%first_name% %last_name%'
      $alias = explode("%", $alias);
      $match = '';
      $num = 1;
      while (!empty($alias)) {
        $next = array_pop($alias);
        if ($num % 2 == 0) { // match column
          if (empty($match)) {
            $match = "$next";
          } else {
            $match = "concat($next, $match)";
          }
        } elseif (!empty($next)) { // in between string
          if (empty($match)) {
            $match = "'$next'";
          } else {
            $match = "concat('$next', $match)";
          }
        }
        $num++;
      }
      if (empty($value)) {
        $value = [];
      } elseif (!is_array($value)) {
        $value = explode(",", preg_replace("/[,\s]*,[,\s]*/", ",", $value));
      }
      $values = [];
      foreach ($value as $v) {
        $row = $this->db->query($referenced_model[0])->select($referenced_model[1])->condition($match, $v)->one();
        if (!empty($row)) {
          $v = $row[$referenced_model[1]];
        }
        $values[] = $v;
      }
      $value = implode(",", $values);
    }
    return $value;
  }
}
