<?php
namespace Starbug\State;

use Starbug\Core\DatabaseInterface;

class DatabaseState implements StateInterface {

  /**
   * Static state cache.
   *
   * @var array
   */
  protected $cache = [];


  public function __construct(DatabaseInterface $db) {
    $this->db = $db;
  }

  public function get($key, $default = null) {
    $values = $this->getMultiple([$key]);
    return isset($values[$key]) ? $values[$key] : $default;
  }

  public function getMultiple(array $keys) {
    $values = [];
    $load = [];
    foreach ($keys as $key) {
      // Check if we have a value in the cache.
      if (isset($this->cache[$key])) {
        $values[$key] = $this->cache[$key];
      } elseif (!array_key_exists($key, $this->cache)) {
        $load[] = $key;
      }
    }
    if ($load) {
      $loaded_values = $this->getValues($load);
      foreach ($load as $key) {
        // If we find a value, even one that is NULL, add it to the cache and
        // return it.
        if (isset($loaded_values[$key]) || array_key_exists($key, $loaded_values)) {
          $values[$key] = $loaded_values[$key];
          $this->cache[$key] = $loaded_values[$key];
        } else {
          $this->cache[$key] = null;
        }
      }
    }
    return $values;
  }


  public function set($key, $value) {
    $this->cache[$key] = $value;
    $this->setValues([$key => $value]);
  }

  /**
   * {@inheritdoc}
   */
  public function setMultiple(array $data) {
    foreach ($data as $key => $value) {
      $this->cache[$key] = $value;
    }
    $this->setValues($data);
  }

  /**
   * {@inheritdoc}
   */
  public function delete($key) {
    unset($this->cache[$key]);
    $this->deleteValues([$key]);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMultiple(array $keys) {
    foreach ($keys as $key) {
      unset($this->cache[$key]);
    }
    $this->deleteValues($keys);
  }

  /**
   * {@inheritdoc}
   */
  public function resetCache() {
    $this->cache = [];
  }

  protected function getValues(array $keys) {
    $values = [];
    $state = $this->db->query("state")->condition("name", $keys)->all();
    foreach ($state as $record) {
      $values[$record["name"]] = json_decode($record["value"], true);
    }
    return $values;
  }

  protected function setValues(array $values) {
    foreach ($values as $key => $value) {
      $state = ["name" => $key, "value" => json_encode($value)];
      $exists = $this->db->query("state")->condition("name", $key)->one();
      if ($exists) {
        $state["id"] = $exists["id"];
      }
      $this->db->store("state", $state);
    }
  }

  protected function deleteValues(array $keys) {
    $this->db->query("state")->condition("name", $keys)->delete();
  }
}
