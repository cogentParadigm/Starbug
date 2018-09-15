<?php
namespace Starbug\Core\Storage\Adapter;

use Starbug\Core\Storage\AdapterInterface;
use Starbug\Core\URLInterface;
use Litipk\Flysystem\Fallback\FallbackAdapter as ParentAdapter;

class FallbackAdapter extends ParentAdapter implements AdapterInterface {
  protected $url;
  public function setURLInterface(URLInterface $url) {
    $this->url = $url;
  }
  public function getURL($path, $absolute = false) {
    if ($this->mainAdapter->has($path)) {
      return $this->mainAdapter->getURL($path, $absolute);
    }
    return $this->fallback->getURL($path, $absolute);
  }
}