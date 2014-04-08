<?php
class hook_display_default {
	function build($display, $field, &$options, $column) {
		efault($options['default'], $column['default']);
	}
}
?>
