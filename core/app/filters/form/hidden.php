<?php
		$field['type'] = 'hidden';
		$field['nolabel'] = $field['nodiv'] = true;
		//POSTed or default value
		$var = $this->get($field['name']);
		if (!empty($var)) $field['value'] = htmlentities($var);
		else if (!empty($field['default'])) {
			$field['value'] = $field['default'];
			unset($field['default']);
		}
		$control = "input";
?>
