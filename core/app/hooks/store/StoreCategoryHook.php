<?php
namespace Starbug\Core;

use Starbug\Db\Schema\SchemaInterface;

class StoreCategoryHook extends QueryHook {
  protected $db;
  protected $schema;
  public function __construct(DatabaseInterface $db, SchemaInterface $schema) {
    $this->db = $db;
    $this->schema = $schema;
  }
  public function validate($query, $key, $value, $column, $argument) {
    if (!empty($value) && !is_numeric($value) && "NULL" !== $value) {
      $field = $this->schema->getColumn($query->model, $column);
      $taxonomy = (empty($field["taxonomy"])) ? $query->model."_".$column : $field['taxonomy'];
      $term = $this->db->query("terms")
        ->condition("taxonomy", $taxonomy)
        ->condition(
          $query->createCondition()
            ->condition("term", $value)
            ->orCondition("slug", $value)
        )->one();
      if ($term) {
        $value = $term["id"];
      }
    }
    return $value;
  }
  public function store($query, $key, $value, $column, $argument) {
    if (!empty($value) && !is_numeric($value) && "NULL" !== $value) {
      $this->db->error("Term not valid", $column, $query->model);
    }
    return $value;
  }
}
