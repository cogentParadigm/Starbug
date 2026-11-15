<?php
namespace Starbug\Core;

use Starbug\Files\ImagesInterface;

class ImagesHelper {
  public function __construct(
    protected ImagesInterface $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
