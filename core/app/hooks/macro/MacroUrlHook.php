<?php
namespace Starbug\Core;

use Starbug\Http\UriBuilderInterface;

class MacroUrlHook extends MacroHook {
  protected $url;
  public function __construct(UriBuilderInterface $uri) {
    $this->uri = $uri;
  }
  public function replace($macro, $name, $token, $data) {
    return isset($data['absolute_urls']) ? $this->uri->build($name, $data['absolute_urls']) : $this->uri->build($name);
  }
}
