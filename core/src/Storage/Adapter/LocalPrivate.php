<?php
namespace Starbug\Core\Storage\Adapter;

use Starbug\Core\Storage\AdapterInterface;
use Starbug\Http\UrlInterface;
use League\Flysystem\Adapter\Local as ParentAdapter;
use League\Flysystem\Config;

class LocalPrivate extends ParentAdapter implements AdapterInterface {
  protected $url;
  public function setUrlInterface(UrlInterface $url) {
    $this->url = $url;
  }
  public function getUrl($path, $absolute = false) {
    if (!is_numeric($path)) {
      [$path, $_] = explode("_", $path);
    }
    return $this->url->build($path, $absolute);
  }
  public function write($path, $contents, Config $config) {
    $config->set("visibility", "private");
    return parent::write($path, $contents, $config);
  }
  public function writeStream($path, $resource, Config $config) {
    $config->set("visibility", "private");
    return parent::writeStream($path, $resource, $config);
  }
}
