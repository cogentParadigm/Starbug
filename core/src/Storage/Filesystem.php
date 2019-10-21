<?php
namespace Starbug\Core\Storage;

use League\Flysystem\Filesystem as ParentFilesystem;
use League\Flysystem\Util;

class Filesystem extends ParentFilesystem implements FilesystemInterface {
  // override constructor to enforce Starbug\Core\Storage\AdapterInterface
  public function __construct(AdapterInterface $adapter, $config = null) {
    parent::__construct($adapter, $config);
  }
  public function getUrl($path, $absolute = false) {
    $path = Util::normalizePath($path);
    return $this->getAdapter()->getUrl($path, $absolute);
  }
}
