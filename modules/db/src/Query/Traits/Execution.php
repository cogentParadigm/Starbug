<?php
namespace Starbug\Db\Query\Traits;

use Starbug\Db\DatabaseInterface;
use ArrayIterator;
use Traversable;

trait Execution {
  public function getDatabase(): DatabaseInterface {
    return $this->db;
  }
  public function validate() {
    return $this->executor->validate($this);
  }
  public function execute() {
    return $this->executor->execute($this);
  }
  public function one() {
    return $this->limit(1)->execute();
  }
  public function all() {
    $records = $this->execute();
    $limit = $this->query->getLimit();
    return ((!empty($limit)) && ($limit == 1)) ? [$records] : $records;
  }
  public function insert($run = true) {
    $this->mode("insert");
    if ($run) {
      return $this->execute();
    }
    return $this;
  }
  public function update($run = true) {
    $this->mode("update");
    if ($run) {
      return $this->execute();
    }
    return $this;
  }
  public function delete($run = true) {
    $this->mode("delete");
    if ($run) {
      return $this->execute();
    }
    return $this;
  }
  public function truncate($run = true) {
    $this->mode("truncate");
    if ($run) {
      return $this->execute();
    }
    return $this;
  }
  public function unsafeTruncate() {
    $this->db->exec("SET FOREIGN_KEY_CHECKS=0");
    $result = $this->truncate();
    $this->db->exec("SET FOREIGN_KEY_CHECKS=1");
    return $result;
  }
  public function count(array $params = []) {
    return $this->executor->count($this, $params);
  }
  public function getId() {
    if ($this->query->isInsert()) {
      return $this->query->getValue("id");
    } elseif ($this->query->isUpdate()) {
      if ($this->query->hasValue("id")) {
        return $this->query->getValue("id");
      } else {
        $record = $this->query($this->query->getTable()->getName())->condition($this->query->getCondition())->one();
        return $record['id'];
      }
    } elseif ($this->query->isDelete()) {
      return $this->query->getValue("id");
    }
  }

  public function interpolate($params = null) {
    return $this->executor->interpolate($this->query, $params);
  }

  public function getIterator(): Traversable {
    return new ArrayIterator($this->execute());
  }
}
