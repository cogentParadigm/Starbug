<?php
namespace Starbug\Js\Helper;

use Starbug\Js\DojoConfiguration;

class DojoHelper {
  public function __construct(DojoConfiguration $target) {
    $this->target = $target;
  }
  public function helper() {
    return $this->target;
  }
}
