<?php
namespace Starbug\Core;

/**
 * Controller factory interface.
 */
interface ControllerFactoryInterface {
  public function get($controller);
}
