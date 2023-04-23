<?php
namespace Starbug\Core;

use Starbug\Db\Query\ExecutorHook;
use Starbug\Auth\SessionHandlerInterface;

class StoreOwnerHook extends ExecutorHook {
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function emptyBeforeInsert($query, $column, $argument) {
    $query->set($column, ($this->session->loggedIn() ? $this->session->getUserId() : "NULL"));
  }
  public function validate($query, $key, $value, $column, $argument) {
    return $this->session->loggedIn() ? $this->session->getUserId() : "NULL";
  }
}
