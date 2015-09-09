<?php
namespace Starbug\Core;
class hook_display_default extends DisplayHook {
	function build($display, $field, &$options, $column) {
		if (empty($options['default'])) $options['default'] = $column['default'];
	}
}
?>
