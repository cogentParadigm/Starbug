<?php
namespace Starbug\Files\Storage\Adapter;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Starbug\Files\Storage\AdapterInterface;
use Starbug\Http\UriBuilderInterface;

class Local extends LocalFilesystemAdapter implements AdapterInterface {
  protected ?UriBuilderInterface $uri = null;
  public function setUriBuilder(UriBuilderInterface $uri) {
    $this->uri = $uri;
  }
  public function getUrl($path, $absolute = false) {
    return $this->uri->build($path, $absolute);
  }
}
