<?php
namespace Starbug\Core\Generator\Definitions;

use Starbug\Core\Generator\Definition;

/**
 * Controller generator.
 */
class Collection extends Definition {
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
      "generate/collection/collection.php",
      $this->module["path"]."/collections/".ucwords($options["name"])."Controller.php"
    );
  }
}
