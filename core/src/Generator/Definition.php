<?php

namespace Starbug\Core\Generator;

use Starbug\Modules\Configuration;

/**
 * Definition is a representation object used by the generator. It describes:
 * - directories to be created
 * - files to be copied
 * - files to be generated from templates
 */
class Definition {
  protected $directories = [];
  protected $templates = [];
  protected $copy = [];
  protected $parameters = [];
  protected $module = "app";
  protected $modules;

  public function __construct(Configuration $modules) {
    $this->modules = $modules;
  }
  /**
   * Set module.
   *
   * @param string $module The module to create files in.
   *
   * @return void
   */
  public function setModule($module) {
    $this->module = $this->modules->get($module) + ["name" => $module];
    $this->setParameter("module", $this->module);
    $namespace = $this->module["namespace"];
    if ($this->hasParameter("dir")) {
      $namespace .= "\\".$this->getParameter("dir");
    }
    $this->setParameter("namespace", $namespace);
  }
  public function getModule() {
    return $this->module;
  }
  public function addDirectory($dir) {
    $this->directories[] = $dir;
  }
  /**
   * Get directories to generate.
   *
   * @return array A list of directories that should be created.
   */
  public function getDirectories() {
    return $this->directories;
  }
  public function addCopy($source, $destination) {
    $this->copy[$source] = $destination;
  }
  public function getCopies() {
    return $this->copy;
  }
  public function addTemplate($source, $destination) {
    $this->templates[$destination] = $source;
  }
  public function getTemplates() {
    return $this->templates;
  }
  public function setParameter($key, $value) {
    $this->parameters[$key] = $value;
  }
  public function getParameter($key) {
    return $this->parameters[$key];
  }
  public function hasParameter($key) {
    return isset($this->parameters[$key]);
  }
  public function getParameters() {
    return $this->parameters;
  }
  public function build(array $options = []) {
    $this->parameters = $options;
    if (!empty($options["module"])) {
      $this->setModule($options["module"]);
    }
  }
  public function reset() {
    $this->directories = [];
    $this->templates = [];
    $this->copy = [];
    $this->parameters = [];
  }
}
