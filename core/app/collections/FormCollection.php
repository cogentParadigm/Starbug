<?php
namespace Starbug\Core;

use Starbug\Db\Schema\SchemaInterface;

class FormCollection extends Collection {
  public $copying = false;
  public function __construct(ModelFactoryInterface $models, SchemaInterface $schema) {
    $this->models = $models;
    $this->schema = $schema;
  }
  public function build($query, $ops) {
    $table = $this->schema->getTable($this->model);
    $query->condition($query->model.".id", $ops['id']);
    $fields = $table->getColumns();
    if ($table->hasOption("base")) {
      unset($fields["id"]);
      foreach ($this->schema->getEntityChain($table->getOption("base")) as $b) {
        unset($fields[$b."_id"]);
      }
    }
    foreach ($fields as $fieldname => $field) {
      if ($this->models->has($field['type'])) {
        if (empty($field['column'])) {
          $field['column'] = "id";
        }
        $query->select("GROUP_CONCAT(".$query->model.'.'.$fieldname.'.'.$field['column'].') as '.$fieldname);
      }
    }
    $parent = $table->getOption("base");
    while (!empty($parent)) {
      foreach ($this->schema->getTable($parent)->getColumns() as $column => $field) {
        if ($this->models->has($field['type'])) {
          if (empty($field['column'])) {
            $field['column'] = "id";
          }
          $query->select("GROUP_CONCAT(".$query->model.'.'.$column.'.'.$field['column'].') as '.$column);
        }
      }
      $parent = $this->schema->getTable($parent)->getOption("base");
    }
    return $query;
  }
  public function filterQuery($query, $ops) {
    if (!empty($ops['copy'])) {
      $this->copying = true;
    }
    $query = parent::filterQuery($query, $ops);
    return $query;
  }
}
