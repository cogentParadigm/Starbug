<?php
namespace Starbug\Core\Storage\Adapter;

use Starbug\Core\Storage\AdapterInterface;
use Starbug\Http\UrlInterface;
use Twistor\Flysystem\Http\HttpAdapter as ParentAdapter;

class HttpAdapter extends ParentAdapter implements AdapterInterface {
  protected $url;
  public function setUrlInterface(UrlInterface $url) {
    $this->url = $url;
  }
  public function getUrl($path, $absolute = false) {
    return $this->url->build($path, true);
  }
}
