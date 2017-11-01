<?php
namespace Starbug\Core\Generator\Definitions;

use Starbug\Core\Generator\Definition;

/**
 * Controller generator.
 */
class Controller extends Definition {
  /**
   * {@inheritdoc}
   *
   * @param array $options Parameters to pass to the generator.
   *
   * @return void
   */
  public function build(array $options = []) {
    parent::build($options);
    $this->addTemplate(
      "generate/controller/controller",
      $this->module."/controllers/".ucwords($options["name"])."Controller.php"
    );
  }
}
