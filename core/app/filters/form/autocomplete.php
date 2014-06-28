<?php
		$field['type'] = 'text';
		$field['autocomplete'] = "off";
		$field['data-dojo-type'] = "starbug/form/Autocomplete";
		if (!isset($field['data-dojo-props'])) {
			$query_action = empty($field['action']) ? "select" : $field['action'];
			$query = empty($field['query']) ? "{}" : $field['query'];
			$field['data-dojo-props'] = array();
			$field['data-dojo-props'][] = "store:sb.get('".$this->model."', '".$query_action."')";
			$field['data-dojo-props'][] = "query:".$query;
			$field['data-dojo-props'] = implode(", ", $field['data-dojo-props']);
		}
		$field['div'] = "autocomplete";
		//POSTed or default value
		$var = $this->get($field['name']);
		if (!empty($var)) $field['value'] = htmlentities($var, ENT_QUOTES, "UTF-8");
		else if (!empty($field['default'])) {
			$field['value'] = $field['default'];
			unset($field['default']);
		}
		$control = "input";
?>
