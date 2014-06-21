<?php
	$value = $this->get($field['name']);
	if ((empty($value)) && (!empty($field['default']))) {
		$this->set($field['name'], $field['default']);
		unset($field['default']);
	}
	efault($field['taxonomy'], ((empty($this->model)) ? "" : $this->model."_").$field['name']);
	efault($field['parent'], 0);
	$terms = terms($field['taxonomy'], $field['parent']);
	$field["value"] = $this->get($field['name']);
	$field["terms"] = $terms;
	$field["writable"] = isset($field['writable']);
?>
