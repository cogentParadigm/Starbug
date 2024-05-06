<?php
namespace Starbug\Core;

use League\Flysystem\MountManager;

class FilesystemsHelper {
  public function __construct(
    protected MountManager $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
