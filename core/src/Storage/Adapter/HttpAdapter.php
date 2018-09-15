<?php
namespace Starbug\Core\Storage\Adapter;

use Starbug\Core\Storage\AdapterInterface;
use Starbug\Core\URLInterface;
use Twistor\Flysystem\Http\HttpAdapter as ParentAdapter;

class HttpAdapter extends ParentAdapter implements AdapterInterface {
  protected $url;
  public function setURLInterface(URLInterface $url) {
    $this->url = $url;
  }
  public function getURL($path, $absolute = false) {
    return $this->url->build($path, true);
  }
}