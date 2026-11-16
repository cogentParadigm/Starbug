<?php
namespace Starbug\Files\Storage;

use League\Flysystem\FilesystemOperator;
use League\Flysystem\MountManager as ParentManager;
use Starbug\Files\Storage\FilesystemInterface;
use UnexpectedValueException;

/**
 * Adds prefix lookup to the mount manager, which flysystem 3 keeps private.
 */
class MountManager extends ParentManager {
  protected array $filesystems = [];
  public function __construct(array $filesystems = [], array $config = []) {
    $this->filesystems = $filesystems;
    parent::__construct($filesystems, $config);
  }
  public function getFilesystem($prefix): FilesystemInterface {
    if (!isset($this->filesystems[$prefix])) {
      throw new UnexpectedValueException("No filesystem is mounted with the prefix ".$prefix);
    }
    return $this->filesystems[$prefix];
  }
}
