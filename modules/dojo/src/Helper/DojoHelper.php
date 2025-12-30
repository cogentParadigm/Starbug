<?php
namespace Starbug\Dojo\Helper;

use Starbug\Dojo\Service\DojoConfiguration;

class DojoHelper {
  public function __construct(
    protected DojoConfiguration $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
