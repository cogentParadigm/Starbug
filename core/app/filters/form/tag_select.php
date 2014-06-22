<?php
	$value = $this->get($field['name']);	
	if ((empty($value)) && (!empty($field['default']))) {
		$this->set($field['name'], $field['default']);
		unset($field['default']);
	}
	if (!is_array($value)) $value = explode(",", $value);
	foreach ($value as $idx => $v) {
		if (substr($v, 0, 1) == "-") unset($value[$idx]);
	}
	assign("value", $value);
?>
