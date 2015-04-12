<?php
		//POSTed or default value
		$var = $this->get($field['name']);
		if (!empty($var)) {
			if (is_array($var)) {
				foreach ($var as $idx => $v) if (substr($v, 0, 1) !== "-") $var[$idx] = htmlentities($v, ENT_QUOTES, "UTF-8");
				$field['value'] = $var;
			} else {
				$field['value'] = htmlentities($var, ENT_QUOTES, "UTF-8");
			}
		}
		else if (!empty($field['default'])) {
			$field['value'] = $field['default'];
			unset($field['default']);
		}
?>
