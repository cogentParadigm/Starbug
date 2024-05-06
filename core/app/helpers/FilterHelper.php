<?php
namespace Starbug\Core;

class FilterHelper {
  public function __construct(
    protected InputFilterInterface $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
