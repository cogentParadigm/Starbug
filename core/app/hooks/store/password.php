<?php
namespace Starbug\Core;
class hook_store_password extends QueryHook {
	function validate(&$query, $key, $value, $column, $argument) {
		return (empty($value) ? $value : Session::hash_password($value));
	}
}
?>
