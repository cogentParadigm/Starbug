<?php
namespace Starbug\Core\Routing\Traits;

trait Resolvers {
  protected $resolvers = [];
  public function addResolver($name, $resolver, $type = "inbound") {
    if (!is_array($resolver)) $resolver = ["resolver" => $resolver];
    $this->resolvers[$name] = $resolver + ["type" => $type];
    return $this;
  }
  public function getResolver($name) {
    return $this->resolvers[$name];
  }
  public function getResolvers($type = false) {
    if (false == $type) return $this->resolvers;
    return array_filter($this->resolvers, function ($resolver) use ($type) {
      return $resolver["type"] == $type;
    });
  }
  public function hasResolver($name) {
    return isset($this->resolvers[$name]);
  }
  public function hasResolvers($type = false) {
    return count($this->getResolvers($type)) > 0;
  }
  public function removeResolver($name) {
    unset($this->resolvers[$name]);
    return $this;
  }
  public function setResolvers($resolvers) {
    $this->resolvers = [];
    foreach ($resolvers as $name => $resolver) {
      $this->addResolver($name, $resolver);
    }
    return $this;
  }
  public function resolve($name, $resolver, $type = "inbound") {
    return $this->addResolver($name, $resolver, $type);
  }
}
