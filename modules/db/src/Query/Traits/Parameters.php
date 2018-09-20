<?php
namespace Starbug\Db\Query\Traits;

trait Parameters {
  protected $parameters = [];

  public function setParameter($name, $value = null) {
    if (!is_array($name)) $name = array($name => $value);
    foreach ($name as $k => $v) {
      if (0 !== strpos($k, ":")) {
        $k = ":" . $k;
      }
      $this->parameters[$k] = $v;
    }
  }

  public function getParameter($name) {
    if (0 !== strpos($name, ":")) {
      $name = ":" . $name;
    }
    return $this->parameters[$name];
  }

  public function getParameters() {
    return $this->parameters;
  }
}
