<?php
	$value = $this->get($field['name']);
	if ((empty($value)) && (!empty($field['default']))) {
		$this->set($field['name'], $field['default']);
		unset($field['default']);
	}
	if (!is_array($value)) $value = explode(",", $value);
	foreach ($value as $idx => $v) {
		if (empty($v) || substr($v, 0, 1) == "-") unset($value[$idx]);
	}

	$info = $this->schema[$field['name']];
	if (sb()->db->has($info['type'])) {
		if (empty($field['from'])) $field['from'] = $info['type'];
		if (empty($field['query'])) $field['query'] = "select";
	}
	$this->assign("value", $value);
?>
