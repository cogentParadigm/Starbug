<?php
namespace Starbug\Db\Query;

use Starbug\Core\DatabaseInterface;
use Starbug\Db\Schema\SchemerInterface;

class Entity implements EntityInterface {

  protected $db;
  protected $schema;

  /**
   * Table constructor.
   *
   * @param DatabaseInterface $db The database connection.
   * @param SchemerInterface $schemer The schema provider.
   */
  public function __construct(DatabaseInterface $db, SchemerInterface $schemer) {
    $this->db = $db;
    $this->schema = $schemer->getSchema();
  }

  /**
   * Query helper to provide a query with all tables joined and columns selected.
   *
   * @param string $entity the name of the entity.
   */
  public function query($entity) {
    $chain = $this->schema->getEntityChain($entity);
    $root = count($chain)-1;

    // build query.
    foreach ($chain as $idx => $name) {
      $collection = ($name === $entity) ? $entity : $entity."_".$name;
      if ($idx === 0) {
        $query = $this->db->query($name." as ".$collection);
      } else {
        $query->join($name." as ".$collection, "INNER");
        if ($idx == $root) {
          $query->on($collection.".id=".$entity.".".$name."_id");
        } else {
          $query->on($collection.".".$chain[$root]."_id=".$entity.".".$chain[$root]."_id");
        }
      }
    }

    // add selection.
    $reverse = array_reverse($chain);
    foreach ($reverse as $idx => $name) {
      $collection = ($name === $entity) ? $entity : $entity."_".$name;
      $query->select("*", $collection);
    }

    return $query;
  }

  /**
   * Load an entity by id.
   *
   * @param int $id the id of the entity to load
   * @param boolean $reset set to true if you don't want to load from cache
   * @param string $name the name of the entity
   */
  public function load($name, $id, $reset = false) {
    static $entities = [];
    $key = $name;
    if (is_array($id)) {
      $conditions = $id;
      $id = false;
    } else {
      $key .= '-'.$id;
    }
    if ($reset || !$id || !isset($entities[$key])) {
      if ($id) {
        $entities[$key] = $this->query($name)->condition($name.".id", $id)->one();
      } elseif ($conditions) {
        $entity = $this->query($name)->conditions($conditions)->one();
        if ($entity) {
          $id = $entity["id"];
          $entities[$name."-".$id] = $entity;
        }
      }
    }
    return $entities[$name."-".$id] ?? null;
  }

  /**
   * Save an entity.
   *
   * @param array $fields the properties to save
   * @param array $from the conditions to match on instead of an ID. must map to a single entity
   * @param string $name the name of the entity
   */
  public function store($name, $fields, $from = []) {
    $original = $update = false;

    if (!empty($fields["id"])) {
      $update = true;
      $original = $this->load($name, $fields["id"], true);
    } elseif (!empty($from)) {
      $original = $this->load($name, $from, true);
      if ($original) {
        $update = true;
        $fields["id"] = $original["id"];
      }
    }

    $chain = $this->schema->getEntityChain($name);
    $last = count($chain)-1;

    foreach ($chain as $idx => $name) {
      if ($idx < $last) {
        $record = [];
        foreach ($this->schema->getTable($name)->getColumns() as $column => $hooks) {
          if ($column !== "id" && $column !== $chain[$last]."_id" && isset($fields[$column])) {
            $record[$column] = $fields[$column];
            unset($fields[$column]);
          }
        }
        if ($update) {
          if (!empty($record)) {
            $this->db->queue($name, $record, [$chain[$last]."_id" => $original[$chain[$last]."_id"]]);
          }
        } else {
          $record[$chain[$last]."_id"] = "";
          $this->db->queue($name, $record);
        }
      } else {
        if ($last > 0) {
          unset($fields[$chain[$last]."_id"]);
          if ($update) {
            $fields["id"] = $original[$chain[$last]."_id"];
          }
        }
        $this->db->store($name, $fields);
      }
      if ($idx > 0 && $this->db->errors($name)) {
        $errors = $this->db->errors[$name];
        unset($this->db->errors[$name]);
        foreach ($errors as $field => $e) {
          $this->db->errors[$chain[0]][$field] = $e;
        }
      }
    }
  }

  /**
   * Delete an entity by id.
   *
   * @param int $id the id of the item to delete
   * @param string $name the entity name
   */
  public function remove($name, $id) {
    $original = $this->load($name, $id, false);
    if (!$original) {
      return;
    }

    if (!$this->schema->getTable($name)->hasOption("base")) {
      $this->db->remove($name, ["id" => $id]);
      return;
    }

    $chain = $this->schema->getEntityChain($name);
    $last = count($chain)-1;

    foreach ($chain as $idx => $name) {
      if ($idx < $last) {
        $this->db->remove($name, [$chain[$last]."_id" => $original[$chain[$last]."_id"]]);
      } else {
        $this->db->remove($name, ["id" => $original[$name."_id"]]);
      }
    }
  }
}
