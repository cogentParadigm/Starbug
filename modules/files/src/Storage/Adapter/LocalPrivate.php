<?php
namespace Starbug\Files\Storage\Adapter;

use Starbug\Files\Storage\AdapterInterface;
use League\Flysystem\Adapter\Local as ParentAdapter;
use League\Flysystem\Config;
use Starbug\Http\UriBuilderInterface;

class LocalPrivate extends ParentAdapter implements AdapterInterface {
  protected $uri;
  public function setUriBuilder(UriBuilderInterface $uri) {
    $this->uri = $uri;
  }
  public function getUrl($path, $absolute = false) {
    if (!is_numeric($path)) {
      [$path, $_] = explode("_", $path);
    }
    return $this->uri->build($path, $absolute);
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
