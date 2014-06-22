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
	//efault($field['taxonomy'], ((empty($this->model)) ? "" : $this->model."_").$field['name']);
	//efault($field['parent'], 0);
	//$terms = terms($field['taxonomy'], $field['parent']);
	assign("value", $value);
	//assign("terms", $terms);
	//assign("writable", isset($field['writable']));
?>
