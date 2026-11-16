<?php
namespace Starbug\Files\Storage\Adapter;

use League\Flysystem\Config;
use League\Flysystem\Visibility;

class LocalPrivate extends Local {
  public function write(string $path, string $contents, Config $config): void {
    parent::write($path, $contents, $this->privateVisibility($config));
  }
  public function writeStream(string $path, $contents, Config $config): void {
    parent::writeStream($path, $contents, $this->privateVisibility($config));
  }
  public function getUrl($path, $absolute = false) {
    if (!is_numeric($path)) {
      [$path, $_] = explode("_", $path);
    }
    return $this->uri->build($path, $absolute);
  }
  protected function privateVisibility(Config $config) {
    return $config->withDefaults([Config::OPTION_VISIBILITY => Visibility::PRIVATE]);
  }
}
