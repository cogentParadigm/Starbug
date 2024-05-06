<?php
namespace Starbug\Core;

use Starbug\Http\UriBuilderInterface;

class UrlHelper {
  public function __construct(
    protected UriBuilderInterface $target
  ) {
  }
  public function helper() {
    return $this->target;
  }
}
