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
}
