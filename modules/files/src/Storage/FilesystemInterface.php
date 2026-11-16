<?php
namespace Starbug\Files\Storage;

use League\Flysystem\FilesystemOperator;

interface FilesystemInterface extends FilesystemOperator {
  public function getUrl($path, $absolute = false);
}
