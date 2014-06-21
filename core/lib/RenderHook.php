<?php
$sb->provide("core/lib/RenderHook");
class RenderHook {
	/**
	 * hook into field rendering
	 * @param string $model the name of the model that the field belongs to
	 * @param array $row the row that this field should be rendered from
	 * @param string $field the name of the field to render
	 * @param array $options formatting options
	 */
	function render($model, $row, $field, $options) {
		return $options;
	}
}
?>
