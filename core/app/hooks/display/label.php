<?php
class hook_display_label {
	function build($display, $field, &$options, $column) {
		efault($options['label'], $column['label']);
	}
}
?>
