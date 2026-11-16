<?php
namespace Starbug\Files\Helper;

use Starbug\Files\Storage\MountManager;

class FilesystemsHelper {
  public function __construct(
    protected MountManager $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
