<?php
namespace Starbug\Core;
class hook_store_md5 extends QueryHook {
	function validate(&$query, $key, $value, $column, $argument) {
		return (empty($value) ? "" : md5($value));
	}
}
?>
