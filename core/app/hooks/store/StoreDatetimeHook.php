<?php
namespace Starbug\Core;
class StoreDatetimeHook extends QueryHook {
	function validate($query, $key, $value, $column, $argument) {
		return (empty($value)) ? $value : date('Y-m-d H:i:s', strtotime($value));
	}
}
