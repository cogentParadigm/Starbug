<?php
namespace Starbug\Core;

use Starbug\Db\Schema\SchemaInterface;

class StoreAliasHook extends QueryHook {
  protected $db;
  protected $schema;
  public function __construct(DatabaseInterface $db, SchemaInterface $schema) {
    $this->db = $db;
    $this->schema = $schema;
  }
  public function validate($query, $key, $value, $column, $alias) {
    if (!empty($value) && !is_numeric($value) && $value != "NULL") {
      $referenced_model = explode(" ", $this->schema->getColumn($query->model, $column)["references"]);
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
      $row = $this->db->query($referenced_model[0])->select($referenced_model[1])->condition($match, $value)->one();
      if (!empty($row)) {
        $value = $row[$referenced_model[1]];
      }
    }
    return $value;
  }
}
