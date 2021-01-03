<?php
namespace Starbug\Core;

use Starbug\Http\UriBuilderInterface;

class UrlHelper {
  public function __construct(UriBuilderInterface $uri) {
    $this->target = $uri;
  }
  public function helper() {
    return $this->target;
  }
}
