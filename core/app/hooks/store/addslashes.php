<?php
namespace Starbug\Core;
class hook_store_addslashes extends QueryHook {
	function validate(&$query, $key, $value, $column, $argument) {
		return addslashes($value);
	}
}
?>
