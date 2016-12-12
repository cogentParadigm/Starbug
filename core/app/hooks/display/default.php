<?php
namespace Starbug\Core;
class hook_display_default extends DisplayHook {
	function build($display, $field, &$options, $column) {
		if (!isset($options["default"]) && isset($column["default"]) && $column["default"] !== "NULL") {
			$options["default"] = $column["default"];
		}
	}
}
?>
