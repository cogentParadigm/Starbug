<?php
class hook_store_md5 {
	function validate(&$query, $key, $value, $column, $argument) {
		return (empty($value) ? "" : md5($value));
	}
}
?>
