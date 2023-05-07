<?php

namespace Starbug\Core\Generator;

/**
 * CompositeDefinition is a Definition composed of other definitions.
 */
class CompositeDefinition extends Definition {
  protected $definitions = [];
  /**
   * Add a child definition.
   *
   * @param Definition $definition The definition to add.
   *
   * @return void
   */
  public function addDefinition(Definition $definition) {
    $this->definitions[] = $definition;
  }
  /**
   * {@inheritdoc}
   */
  public function getDirectories() {
    $directories = parent::getDirectories();
    foreach ($this->definitions as $definition) {
      $directories = array_merge($directories, $definition->getDirectories());
    }
    return $directories;
  }
  public function getCopies() {
    $copies = parent::getCopies();
    foreach ($this->definitions as $definition) {
      $copies = array_merge($copies, $definition->getCopies());
    }
    return $copies;
  }
  public function getTemplates() {
    $templates = parent::getTemplates();
    foreach ($this->definitions as $definition) {
      $templates = array_merge($templates, $definition->getTemplates());
    }
    return $templates;
  }
  public function getParameter($key) {
    $value = parent::hasParameter($key) ? parent::getParameter($key) : null;
    foreach ($this->definitions as $definition) {
      if ($definition->hasParameter($key)) {
        $value = $definition->getParameter($key);
      }
    }
    return $value;
  }
  public function hasParameter($key) {
    if (parent::hasParameter($key)) {
      return true;
    }
    foreach ($this->definitions as $definition) {
      if ($definition->hasParameter($key)) {
        return true;
      }
    }
    return false;
  }
  public function getParameters() {
    $parameters = parent::getParameters();
    foreach ($this->definitions as $definition) {
      $parameters = array_merge($parameters, $definition->getParameters());
    }
    return $parameters;
  }
  public function build(array $options = []) {
    parent::build($options);
    foreach ($this->definitions as $definition) {
      $definition->build($options);
    }
  }
  public function reset() {
    parent::reset();
    foreach ($this->definitions as $definition) {
      $definition->reset();
    }
  }
}
