<?php
namespace Starbug\Db\Query\Hook;

use Starbug\Db\Query\ExecutorHook;
use Starbug\Auth\SessionHandlerInterface;

class StorePasswordHook extends ExecutorHook {
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) ? $value : $this->session->hashPassword($value));
  }
}
