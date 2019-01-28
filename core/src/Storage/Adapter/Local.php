<?php
namespace Starbug\Core\Storage\Adapter;

use Starbug\Core\Storage\AdapterInterface;
use Starbug\Http\UrlInterface;
use League\Flysystem\Adapter\Local as ParentAdapter;

class Local extends ParentAdapter implements AdapterInterface {
  protected $url;
  public function setUrlInterface(UrlInterface $url) {
    $this->url = $url;
  }
  public function getUrl($path, $absolute = false) {
    return $this->url->build($path, $absolute);
  }
}
