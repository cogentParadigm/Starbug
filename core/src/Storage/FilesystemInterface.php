<?php
namespace Starbug\Core\Storage;

use League\Flysystem\FilesystemInterface as ParentInterface;

interface FilesystemInterface extends ParentInterface {
  public function getURL($path, $absolute = false);
}
