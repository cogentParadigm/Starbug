<?php
namespace Starbug\Core;

use Starbug\Http\UrlInterface;

class UrlHelper {
  public function __construct(UrlInterface $url) {
    $this->target = $url;
  }
  public function helper() {
    return $this->target;
  }
}
