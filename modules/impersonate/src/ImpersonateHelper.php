<?php
namespace Starbug\Users\Impersonate;

class ImpersonateHelper {
  public function isActive() {
    return isset($_COOKIE["iuid"]);
  }
  public function helper() {
    return $this;
  }
}
