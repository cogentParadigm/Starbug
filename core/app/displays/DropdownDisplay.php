<?php
namespace Starbug\Core;
class DropdownDisplay extends ItemDisplay {
	public $type = "list";
	public $template = "select";
	public function build_display($options) {
		$this->attributes['class'][] = $this->template;
		if (!empty($options['attributes'])) {
			if (!empty($options['attributes']['class']) && !is_array($options['attributes']['class'])) $options['attributes']['class'] = array($options['attributes']['class']);
			$this->attributes = array_merge_recursive($this->attributes, $options['attributes']);
		}
		if (!empty($options['model']) && !empty($options['collection'])) {
			$this->model = $options['model'];
			$this->collection = $options['collection'];
		} else if (!empty($options['options'])) {
			foreach ($options['options'] as $option) {
				$this->items[] = array("id" => $option, "label" => $option);
			}
		}
	}
}
