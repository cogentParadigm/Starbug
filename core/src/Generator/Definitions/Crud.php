<?php

namespace Starbug\Core\Generator\Definitions;

use Starbug\Core\Generator\CompositeDefinition;
use Starbug\Modules\Configuration;

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
  public function __construct(Configuration $modules, Form $form, Grid $grid) {
    parent::__construct($modules);
    $this->definitions[] = $form;
    $this->definitions[] = $grid;
  }
}
