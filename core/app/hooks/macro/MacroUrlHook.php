<?php
namespace Starbug\Core;

use Starbug\Http\UriBuilderInterface;

class MacroUrlHook extends MacroHook {
  public function __construct(
    protected UriBuilderInterface $uri
  ) {
  }
  public function replace($macro, $name, $token, $data) {
    return isset($data['absolute_urls']) ? $this->uri->build($name, $data['absolute_urls']) : $this->uri->build($name);
  }
}
