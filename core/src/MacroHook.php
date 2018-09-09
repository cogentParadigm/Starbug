<?php
namespace Starbug\Core;

class MacroHook {
  /**
   * set options when adding/updating fields fields
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  function replace($macro, $name, $token, $data) {
    return $token;
  }
}
