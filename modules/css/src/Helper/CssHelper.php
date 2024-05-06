<?php
namespace Starbug\Css\Helper;

use Starbug\Css\CssLoader;

class CssHelper {
  public function __construct(
    protected CssLoader $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
