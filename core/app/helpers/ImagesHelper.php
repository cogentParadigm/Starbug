<?php
namespace Starbug\Core;

class ImagesHelper {
  public function __construct(
    protected ImagesInterface $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
