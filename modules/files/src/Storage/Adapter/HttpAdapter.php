<?php
namespace Starbug\Files\Storage\Adapter;

use Starbug\Files\Storage\AdapterInterface;
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
