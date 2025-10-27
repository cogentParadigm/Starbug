<?php
namespace Starbug\Files\Storage;

use League\Flysystem\Filesystem as ParentFilesystem;
use League\Flysystem\Util;

class Filesystem extends ParentFilesystem implements FilesystemInterface {
  // override constructor to enforce Starbug\Files\Storage\AdapterInterface
  public function __construct(AdapterInterface $adapter, $config = null) {
    parent::__construct($adapter, $config);
  }
  public function getUrl($path, $absolute = false) {
    $path = Util::normalizePath($path);
    return $this->getAdapter()->getUrl($path, $absolute);
  }
}
