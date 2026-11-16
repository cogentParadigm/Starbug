<?php
namespace Starbug\Files\Storage\Adapter;

use Netzarbeiter\FlysystemHttp\HttpAdapterStream;
use Starbug\Files\Storage\AdapterInterface;
use Starbug\Http\UriBuilderInterface;

/**
 * Read-only adapter over HTTP, so a local filesystem can proxy files from
 * another environment, such as published files on a stage server.
 */
class HttpAdapter extends HttpAdapterStream implements AdapterInterface {
  protected ?UriBuilderInterface $uri = null;
  public function setUriBuilder(UriBuilderInterface $uri) {
    $this->uri = $uri;
  }
  public function getUrl($path, $absolute = false) {
    return $this->uri->build($path, true);
  }
}
