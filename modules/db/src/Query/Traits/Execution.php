<?php
namespace Starbug\Db\Query\Traits;

trait Execution {
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
  function unsafe_truncate() {
    $this->executor->getConnection()->exec("SET FOREIGN_KEY_CHECKS=0");
    $result = $this->truncate();
    $this->executor->getConnection()->exec("SET FOREIGN_KEY_CHECKS=1");
    return $result;
  }
  function count(array $params = []) {
    return $this->executor->count($this->query, $params);
  }
  function getId() {
    if ($this->query->isInsert()) return $this->query->getValue("id");
    elseif ($this->query->isUpdate()) {
      if ($this->query->hasValue("id")) return $this->query->getValue("id");
      else {
        $record = $this->query($this->model)->condition($this->query->getCondition())->one();
        return $record['id'];
      }
    } elseif ($this->mode == "delete") {
      return $this->query->getValue("id");
    }
  }
}
