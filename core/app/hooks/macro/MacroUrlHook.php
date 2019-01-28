<?php
namespace Starbug\Core;

use Starbug\Http\UrlInterface;

class MacroUrlHook extends MacroHook {
  protected $url;
  public function __construct(UrlInterface $url) {
    $this->url = $url;
  }
  public function replace($macro, $name, $token, $data) {
    return isset($data['absolute_urls']) ? $this->url->build($name, $data['absolute_urls']) : $this->url->build($name);
  }
}
