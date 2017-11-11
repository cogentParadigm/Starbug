<?php
namespace Starbug\Db\Query\Traits;

trait Tagging {
  protected $tags = [];

  public function addTag($tag) {
    $this->tags[$tag] = true;
  }

  public function removeTag($tag) {
    unset($this->tags[$tag]);
  }

  public function hasTag($tag) {
    return isset($this->tags[$tag]);
  }
}
