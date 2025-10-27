<?php
namespace Starbug\Files\Storage\Adapter;

use Starbug\Files\Storage\AdapterInterface;
use League\Flysystem\Adapter\Local as ParentAdapter;
use Starbug\Http\UriBuilderInterface;

class Local extends ParentAdapter implements AdapterInterface {
  protected $uri;
  public function setUriBuilder(UriBuilderInterface $uri) {
    $this->uri = $uri;
  }
  public function getUrl($path, $absolute = false) {
    return $this->uri->build($path, $absolute);
  }
}
