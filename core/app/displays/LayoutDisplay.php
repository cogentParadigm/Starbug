<?php
class LayoutDisplay {
	var $type = "layout";
	var $template = "layout";

	var $cells = array();


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
		$this->cells[$parent]->appendChild($html);
	}

	function is_empty() {
		return empty($this->cells);
	}
}
?>
