<?php
namespace Starbug\Files\Storage;

use League\Flysystem\Filesystem as ParentFilesystem;
use League\Flysystem\WhitespacePathNormalizer;

class Filesystem extends ParentFilesystem implements FilesystemInterface {
  public function __construct(protected AdapterInterface $storageAdapter, array $config = []) {
    parent::__construct($storageAdapter, $config);
  }
  public function getUrl($path, $absolute = false) {
    $path = (new WhitespacePathNormalizer())->normalizePath($path);
    return $this->storageAdapter->getUrl($path, $absolute);
  }
}
