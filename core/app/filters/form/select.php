<?php
		$name = $field['name'];
		$value = $this->get($field['name']);
		if ((empty($value)) && (!empty($field['default']))) {
			$this->set($field['name'], $field['default']);
			$value = $field['default'];
			unset($field['default']);
		}
		if (isset($field['multiple'])) {
			$field['multiple'] = "multiple";
			efault($field['size'], 5);
			if (!is_array($value)) $value = explode(',', $value);
		}
		if (!empty($field['range'])) {
			$range = explode("-", $field['range']);
			for ($i=$range[0];$i<=$range[1];$i++) $options[$i] = $i;
			unset($field['range']);
		}
		$mode = "template";
		if (!empty($field['caption'])) {
			if (!empty($field['from'])) {
				$list = $options;
				$options = query($field['from'], $field)->all();
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
		} else {
			$info = $this->schema[$field['name']];
			if (!empty($info['references'])) {
				if (empty($field['from'])) $field['from'] = reset(explode(" ", $info['references']));
				if (empty($field['query'])) $field['query'] = "select";
			}
			if (!empty($field['query']) && !empty($field['from'])) {
				$mode = "display";
			}
		}
		assign("value", $value);
		assign("options", $options);
		assign("mode", $mode);
?>
