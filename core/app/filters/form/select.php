<?php
		$name = $field['name'];
		if (isset($field['multiple'])) {
			$field['multiple'] = "multiple";
			$field['name'] = $field['name']."[]";
			efault($field['size'], 5);
		}
		$value = $this->get($field['name']);
		if ((empty($value)) && (!empty($field['default']))) {
			$this->set($field['name'], $field['default']);
			unset($field['default']);
		}
		if (!empty($field['range'])) {
			$range = explode("-", $field['range']);
			for ($i=$range[0];$i<=$range[1];$i++) $options[$i] = $i;
			unset($field['range']);
		}
		if (!empty($field['caption'])) {
			if (!empty($field['from'])) {
				$list = $options;
				$options = query($field['from'], $field);
			} else $list = array();
			$keys = array();
			if (!empty($options)) foreach ($options[0] as $k => $v) if (false !== strpos($field['caption'], "%$k%")) $keys[] = $k;
			foreach ($options as $o) {
				$cap = $field['caption'];
				foreach($keys as $k) $cap = str_replace("%$k%", $o[$k], $cap);
				$list[$cap] = $o[$field['value']];
			}
			$options = $list; unset($field['caption']); unset($field['value']);
		} else if (!empty($field['options'])) {
			$keys = explode(",", $field['options']);
			$values = (!empty($field['values'])) ? explode(",", $field['values']) : $keys;
			$options = array();
			foreach ($keys as $i => $k) $options[$k] = $values[$i];
			unset($field['options']);
			unset($field['values']);
		}
		assign("value", $this->get($name));
		assign("options", $options);
?>
