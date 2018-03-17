<?php
namespace Starbug\Core;
class LayoutDisplay extends ItemDisplay {
	public $type = "layout";
	public $template = "layout.html";

	public $cells = array();

	public $default_cell = false;

	function __construct(TemplateInterface $output, ResponseInterface $response, ModelFactoryInterface $models, CollectionFactoryInterface $collections, HookFactoryInterface $hook_builder, InputFilterInterface $filter) {
		$this->output = $output;
		$this->models = $models;
		$this->collections = $collections;
		$this->response = $response;
		$this->hook_builder = $hook_builder;
		$this->filter = $filter;
	}
	/**
	 * Allows you to filter the options for each column.
	 * This is useful for adding defaults after the columns are set
	 * or converting common parameters that have been specified to display specific parameters
	 */
	function filter($field, $options, $column) {
		foreach ($options as $k => $v) {
			if ($k !== 'attributes') $this->cells[$k] = Renderable::create($v);
		}
		if (!isset($options['attributes']['class'])) $options['attributes']['class'] = array('row');
		else if (!in_array('row', $options['attributes']['class'])) $options['attributes']['class'][] = 'row';
		return $options;
	}

	function query($options = null) {
		//disable query
	}

	function put($parent, $selector, $content = "", $key = "") {
		$node = Renderable::create($this->cells[$parent], $selector, $content);
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

	function output($match = "") {
		foreach ($this->fields as $name => $field) {
			if (!empty($match) && substr($name, 0, strlen($match)) != $match) continue;
			$field['attributes']['class'] = implode(' ', $field['attributes']['class']);
			$node = '<div '.$this->filter->attributes($field['attributes']).'>';
			foreach ($field as $key => $value) if ($key != 'attributes') $node .= (string) $this->cells[$key];
			$node .= '</div>';
			echo $node;
		}
	}
}
