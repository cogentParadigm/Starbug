<?php
namespace Starbug\Core\Routing\Traits;

trait Status {
  protected $found = true;
  protected $forbidden = false;
  public function found() {
    $this->found = true;
    return $this;
  }
  public function notFound() {
    $this->found = false;
    return $this;
  }
  public function isFound() {
    return $this->found;
  }
  public function isNotFound() {
    return !$this->found;
  }
  public function forbidden() {
    $this->forbidden = true;
    return $this;
  }
  public function notForbidden() {
    $this->forbidden = false;
    return $this;
  }
  public function isForbidden() {
    return $this->forbidden;
  }
  public function isNotForbidden() {
    return !$this->forbidden;
  }
}
