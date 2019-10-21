<?php
namespace Starbug\Js;

class DojoHelper {
  public function __construct(DojoConfiguration $target) {
    $this->target = $target;
  }
  public function helper() {
    return $this->target;
  }
}
