<?php
namespace Starbug\Core;

class MacroHook {
  /**
   * Get options when adding/updating fields fields.
   *
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function replace($macro, $name, $token, $data) {
    return $token;
  }
}
