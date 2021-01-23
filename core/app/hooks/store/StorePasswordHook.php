<?php
namespace Starbug\Core;

use Starbug\Auth\SessionHandlerInterface;

class StorePasswordHook extends QueryHook {
  public function __construct(SessionHandlerInterface $session) {
    $this->session = $session;
  }
  public function validate($query, $key, $value, $column, $argument) {
    return (empty($value) ? $value : $this->session->hashPassword($value));
  }
}
