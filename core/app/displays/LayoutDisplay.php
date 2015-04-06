<?php
class LayoutDisplay extends Display {
	var $type = "layout";
	var $template = "layout";

	var $cells = array();

	var $default_cell = false;


	/**
	 * Allows you to filter the options for each column.
	 * This is useful for adding defaults after the columns are set
	 * or converting common parameters that have been specified to display specific parameters
	 */
	function filter($field, $options, $column) {
		foreach ($options as $k => $v) {
			if ($k !== 'attributes') $this->cells[$k] = put($v);
		}
		if (!isset($options['attributes']['class'])) $options['attributes']['class'] = array('row');
		else if (!in_array('row', $options['attributes']['class'])) $options['attributes']['class'][] = 'row';
		return $options;
	}

	function query($options=null) {
		//disable query
	}

	function put($parent, $selector, $content="", $key="") {
		$node = put($this->cells[$parent], $selector, $content);
		if (!empty($key)) $this->cells[$key] = $node;
		return $node;
	}

	function append($parent, $html) {
		if (empty($parent)) $parent = $this->default_cell;
		else if (!$this->default_cell) $this->default_cell = $parent;
		$this->cells[$parent]->appendChild($html);
	}

	function is_empty() {
		return empty($this->cells);
	}

	function output($match="") {
		foreach ($this->fields as $name => $field) {
				if (!empty($match) && substr($name, 0, strlen($match)) != $match) continue;
				$field['attributes']['class'] = implode(' ',$field['attributes']['class']);
				$node = '<div '.html_attributes($field['attributes'], false).'>';
				foreach ($field as $key => $value) if($key != 'attributes') $node .= (string) $this->cells[$key];
				$node .= '</div>';
				echo $node;
		}
	}
}
?>
