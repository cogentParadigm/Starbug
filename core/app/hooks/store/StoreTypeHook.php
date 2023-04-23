<?php
namespace Starbug\Core;

use Starbug\Db\Query\ExecutorHook;
use Starbug\Db\Schema\SchemaInterface;

class StoreTypeHook extends ExecutorHook {
  public function __construct(DatabaseInterface $db, SchemaInterface $schema) {
    $this->db = $db;
    $this->schema = $schema;
  }
  public function emptyValidate($query, $column, $argument) {
    if ($this->schema->hasTable($argument)) {
      $query->exclude($column);
    }
  }
  public function validate($query, $key, $value, $column, $argument) {
    if ($this->schema->hasTable($argument)) {
      $query->exclude($key);
    }
    return $value;
  }
  public function afterStore($query, $key, $value, $column, $argument) {
    if ($argument == "terms" || $argument == "blocks" || !$this->schema->hasTable($argument)) {
      return;
    }

    // vars
    $model = $query->model;
    $model_id = $query->getId();
    $hooks = $this->schema->getColumn($model, $column);
    $target = (empty($hooks['table'])) ? $model."_".$column : $hooks['table'];
    $type = $argument;
    $type_ids = [];
    $ids = [];
    $clean = true;

    // loop through values
    if (empty($value)) {
      $value = [];
    } elseif (!is_array($value)) {
      $value = explode(",", preg_replace("/[,\s]*,[,\s]*/", ",", $value));
    }
    foreach ($value as $position => $type_id) {
      $remove = false;
      $value_type = ($type == $target) ? "id" : $column."_id";
      if (is_array($type_id)) {
        $value_type = "object";
      } else {
        if (0 === strpos($type_id, "-")) {
          $remove = true;
          $clean = false;
          $type_id = substr($type_id, 1);
        } elseif (0 === strpos($type_id, "+")) {
          $clean = false;
          $type_id = substr($type_id, 1);
        }
        if (0 === strpos($type_id, "#")) {
          $value_type = "id";
          $type_id = substr($type_id, 1);
        }
      }

      if ($value_type === "object") {
        if (isset($type_id['id'])) {
          $entry = $this->db->query($target)->condition("id", $type_id['id']);
          $ids[] = $type_id['id'];
        } else {
          $entry = $this->db->query($target)->conditions([$model."_id" => $model_id, $column."_id" => $type_id[$column."_id"]]);
          $type_ids[] = $type_id[$column."_id"];
        }
        $entry->set($model."_id", $model_id);
        $entry->fields($type_id);
        $entry->set("position", intval($position)+1);
        if (isset($type_id['id']) || $entry->one()) {
          $entry->update();
        } else {
          $entry->insert();
        }
      } elseif ($value_type === "id") {
        $entry = $this->db->query($target)->condition("id", $type_id);
        if ($remove) {
          $entry->delete();
        } else {
          // update
          $entry->set($model."_id", $model_id);
          $entry->set("position", intval($position)+1);
          $entry->update();
          $ids[] = $type_id;
        }
      } else {
        $entry = $this->db->query($target)->conditions([$model."_id" => $model_id, $column."_id" => $type_id]);
        if ($remove) {
          // remove
          $entry->delete();
        } else {
          $record = [$model."_id" => $model_id, $column."_id" => $type_id, "position" => intval($position)+1];
          if ($row = $entry->one()) {
            $record["id"] = $row["id"];
          }
          $this->db->store($target, $record);
          $type_ids[] = $type_id;
        }
      }
    }

    // clean
    if ($clean) {
      $query = $this->db->query($target)->condition($model."_id", $model_id);
      if (!empty($type_ids)) {
        $query->condition($column."_id", $type_ids, "!=");
      }
      if (!empty($ids)) {
        $query->condition("id", $ids, "!=");
      }
      $query->delete();
    }
  }
}
