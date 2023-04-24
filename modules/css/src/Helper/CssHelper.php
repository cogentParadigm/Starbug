<?php
namespace Starbug\Css\Helper;

use Starbug\Css\CssLoader;

class CssHelper {
  public function __construct(CssLoader $target) {
    $this->target = $target;
  }
  public function helper() {
    return $this->target;
  }
}
