<?php
namespace Starbug\Core\Storage\Adapter;

use Starbug\Core\Storage\AdapterInterface;
use Litipk\Flysystem\Fallback\FallbackAdapter as ParentAdapter;
use Starbug\Http\UriBuilderInterface;

class FallbackAdapter extends ParentAdapter implements AdapterInterface {
  /**
   * URI Builder
   *
   * @var UriBuilderInterface
   */
  protected $uri;
  public function setUriBuilder(UriBuilderInterface $uri) {
    $this->uri = $uri;
  }
  public function getUrl($path, $absolute = false) {
    if ($this->mainAdapter->has($path)) {
      return $this->mainAdapter->getUrl($path, $absolute);
    }
    return $this->fallback->getUrl($path, $absolute);
  }
}
