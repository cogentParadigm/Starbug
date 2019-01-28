<?php
namespace Starbug\Core\Storage;

use League\Flysystem\AdapterInterface as ParentInterface;

interface AdapterInterface extends ParentInterface {
  public function getUrl($path, $absolute = false);
}
