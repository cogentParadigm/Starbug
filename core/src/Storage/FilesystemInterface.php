<?php
namespace Starbug\Core\Storage;

use League\Flysystem\FilesystemInterface as ParentInterface;

interface FilesystemInterface extends ParentInterface {
  public function getUrl($path, $absolute = false);
}
