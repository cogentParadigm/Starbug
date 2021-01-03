<?php
namespace Starbug\Core\Storage\Adapter;

use Starbug\Core\Storage\AdapterInterface;
use Starbug\Http\UriBuilderInterface;
use Twistor\Flysystem\Http\HttpAdapter as ParentAdapter;

class HttpAdapter extends ParentAdapter implements AdapterInterface {
  protected $uri;
  public function setUriBuilder(UriBuilderInterface $uri) {
    $this->uri = $uri;
  }
  public function getUrl($path, $absolute = false) {
    return $this->uri->build($path, true);
  }
}
