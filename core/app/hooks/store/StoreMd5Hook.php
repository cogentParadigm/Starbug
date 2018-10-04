<?php
namespace Starbug\Core;
class StoreMd5Hook extends QueryHook {
	function validate($query, $key, $value, $column, $argument) {
		return (empty($value) ? "" : md5($value));
	}
}
