<?php
namespace Starbug\Core;

class DisplayDefaultHook extends DisplayHook {
  public function build($display, $field, &$options, $column) {
    if (!isset($options["default"]) && isset($column["default"]) && $column["default"] !== "NULL") {
      $options["default"] = $column["default"];
    }
  }
}
