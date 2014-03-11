<?php
class hook_store_references {
	function store(&$query, $key, $value, $column, $argument) {
		$model = reset(explode(" ", $argument));
		return (empty($value) ? sb($model)->insert_id : $value);
	}
}
?>
