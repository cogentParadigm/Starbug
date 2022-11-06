<?php
namespace Starbug\Core;

class DisplayLabelHook extends DisplayHook {
  public function build($display, $field, &$options, $column) {
    if (empty($options['label'])) {
      $options['label'] = $column['label'];
    }
  }
}
