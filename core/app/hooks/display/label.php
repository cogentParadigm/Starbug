<?php
class hook_display_label extends DisplayHook {
	function build($display, $field, &$options, $column) {
		if (empty($options['label'])) $options['label'] = $column['label'];
	}
}
?>
