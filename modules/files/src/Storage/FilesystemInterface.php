<?php
namespace Starbug\Files\Storage;

use League\Flysystem\FilesystemInterface as ParentInterface;

interface FilesystemInterface extends ParentInterface {
  public function getUrl($path, $absolute = false);
}
