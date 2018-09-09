<?php
namespace Starbug\Core;

class UserHelper {
  public function __construct(IdentityInterface $user) {
    $this->target = $user;
  }
  public function helper() {
    return $this->target;
  }
}
