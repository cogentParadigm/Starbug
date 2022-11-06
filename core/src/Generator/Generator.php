<?php

namespace Starbug\Core\Generator;

use Starbug\Core\TemplateInterface;

/**
 * Generator runs Definition objects to create directories, copy files, and generate files from templates.
 */
class Generator {
  /**
   * Constructor.
   *
   * @param TemplateInterface $renderer Template renderer.
   * @param boolean $base_directory Project root.
   */
  public function __construct(TemplateInterface $renderer, $base_directory = false) {
    $this->renderer = $renderer;
    $this->base_directory = $base_directory;
    if (false == $this->base_directory) {
      $this->base_directory = getcwd();
    }
  }
  /**
   * Run a Definition object with some parameters.
   *
   * @param Definition $definition The definition to run.
   * @param array $options The parameters to pass to the definition.
   *
   * @return void
   */
  public function generate(Definition $definition, array $options = []) {
    $definition->build($options);
    // CREATE DIRECTORIES.
    foreach ($definition->getDirectories() as $dir) {
      if (!file_exists($this->base_directory."/".$dir)) {
        passthru("mkdir ".$this->base_directory."/".$dir);
      }
    }
    // CREATE FILES.
    foreach ($definition->getTemplates() as $output => $template) {
      $data = $this->renderer->capture($template, $definition->getParameters());
      file_put_contents($this->base_directory."/".$output, $data);
    }
    // COPY FILES.
    foreach ($definition->getCopies() as $origin => $dest) {
      passthru("cp ".$this->base_directory."/$origin ".$this->base_directory."/".$dest);
    }
  }
}
