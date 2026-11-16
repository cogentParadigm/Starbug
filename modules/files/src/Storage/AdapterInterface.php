<?php
namespace Starbug\Files\Storage;

use League\Flysystem\FilesystemAdapter;

interface AdapterInterface extends FilesystemAdapter {
  public function getUrl($path, $absolute = false);
}
