<?php
namespace Starbug\Core\Storage\Adapter;

use Starbug\Core\Storage\AdapterInterface;
use Starbug\Http\UrlInterface;
use Litipk\Flysystem\Fallback\FallbackAdapter as ParentAdapter;

class FallbackAdapter extends ParentAdapter implements AdapterInterface {
  protected $url;
  public function setUrlInterface(UrlInterface $url) {
    $this->url = $url;
  }
  public function getUrl($path, $absolute = false) {
    if ($this->mainAdapter->has($path)) {
      return $this->mainAdapter->getUrl($path, $absolute);
    }
    return $this->fallback->getUrl($path, $absolute);
  }
}
