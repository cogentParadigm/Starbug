<?php

namespace Starbug\Core\Generator\Definitions;

use Starbug\Core\Generator\CompositeDefinition;

/**
 * Generate Crud.
 */
class Crud extends CompositeDefinition {

  /**
   * Constructor.
   *
   * @param Form $form The form definition.
   * @param Grid $grid The grid definition.
   */
  public function __construct(Form $form, Grid $grid) {
    $this->definitions[] = $form;
    $this->definitions[] = $grid;
  }

  /**
   * {@inheritdoc}
   *
   * @param array $options The options to pass in.
   *
   * @return void
   */
  public function build(array $options = []) {
    parent::build($options);
    $className = str_replace(" ", "", ucwords(str_replace("_", " ", $options["model"])));
    $this->setParameter("className", $className);
    $this->addTemplate(
      "generate/crud/controller",
      $this->module."/controllers/Admin".$className."Controller.php"
    );
    $this->addTemplate(
      "generate/crud/api",
      $this->module."/controllers/Api".$className."Controller.php"
    );
  }
}
