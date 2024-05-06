<?php
namespace Starbug\Js\Helper;

use Starbug\Js\DojoConfiguration;

class DojoHelper {
  public function __construct(
    protected DojoConfiguration $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
