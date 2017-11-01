<?php
namespace Starbug\Db\Query;

class CompiledQuery {
  protected $sql = false;
  protected $countSql = false;
  protected $executable = true;

  public function __construct($sql, $countSql = false, $executable = true) {
    $this->sql = $sql;
    $this->countSql = $countSql;
    $this->executable = $executable;
  }

  public function getSql() {
    return $this->sql;
  }

  public function setSql($sql) {
    $this->sql = $sql;
  }

  public function getCountSql() {
    return $this->countSql;
  }

  public function setCountSql($sql) {
    $this->countSql = $countSql;
  }

  public function isExecutable() {
    return $this->executable;
  }

  public function setExecutable($executable = true) {
    $this->executable = $executable;
  }

}