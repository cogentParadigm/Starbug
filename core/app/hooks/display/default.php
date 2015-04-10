<?php
class hook_display_default extends DisplayHook {
	function build($display, $field, &$options, $column) {
		efault($options['default'], $column['default']);
	}
}
?>
