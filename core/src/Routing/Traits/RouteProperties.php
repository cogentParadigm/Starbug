<?php
namespace Starbug\Core\Routing\Traits;

use Starbug\Core\Routing\Route;

trait RouteProperties {
  protected $path;
  protected $controller;
  protected $arguments = [];
  protected $options = [];
  protected $parent;
  public function setPath($path) {
    $this->path = $path;
    return $this;
  }
  public function getPath($absolute = true) {
    if ($this->hasParent() && $absolute) {
      return $this->parent->getPath() . $this->path;
    }
    return $this->path;
  }
  public function setController($controller) {
    $this->controller = $controller;
    return $this;
  }
  public function getController() {
    return $this->controller;
  }
  public function setArguments($arguments) {
    $this->arguments = $arguments;
    return $this;
  }
  public function getArguments() {
    return $this->arguments;
  }
  public function setOption($name, $value) {
    $this->options[$name] = $value;
    return $this;
  }
  public function setOptions($options) {
    $this->options = $options + $this->options;
    return $this;
  }
  public function getOption($name) {
    return $this->getOptions()[$name] ?? null;
  }
  public function getOptions() {
    if ($this->hasParent()) {
      return $this->options + $this->parent->getOptions();
    }
    return $this->options;
  }
  public function hasOption($name) {
    return isset($this->getOptions()[$name]);
  }
  public function setParent(Route $parent) {
    $this->parent = $parent;
    return $this;
  }
  public function getParent(): ?Route {
    return $this->parent;
  }
  public function hasParent() {
    return isset($this->parent);
  }
}
