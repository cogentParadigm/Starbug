<?php
namespace Starbug\Core;

use League\Flysystem\MountManager;

class FilesystemsHelper {
  public function __construct(MountManager $target) {
    $this->target = $target;
  }
  public function helper() {
    return $this->target;
  }
}
