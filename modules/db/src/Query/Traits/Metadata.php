<?php
namespace Starbug\Db\Query\Traits;

trait Metadata {
  protected $metadata = [];

  public function setMeta($name, $value) {
    $this->metadata[$name] = $value;
  }

  public function removeMeta($name) {
    unset($this->metadata[$name]);
  }

  public function hasMeta($name) {
    return isset($this->metadata[$name]);
  }

  public function getMeta($name, $default = null) {
    return isset($this->metadata[$name]) ? $this->metadata[$name] : $default;
  }
}
