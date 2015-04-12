<?php
		$field['type'] = 'checkbox';
		$value = $this->get($field['name']);
		if (($value === '' && $field['value'] == $field['default']) || $value == $field['value']) $field['checked'] = 'checked';
		$control = 'input';
?>
