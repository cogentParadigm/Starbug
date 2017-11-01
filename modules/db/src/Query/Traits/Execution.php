<?php
namespace Starbug\Db\Query\Traits;

trait Execution {
  public function execute() {
    return $this->db->execute($this->query);
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
    $this->db->exec("SET FOREIGN_KEY_CHECKS=0");
    $result = $this->truncate();
    $this->db->exec("SET FOREIGN_KEY_CHECKS=1");
    return $result;
  }
  function count(array $params = []) {
    return $this->db->count($this->query, $params);
  }
}
