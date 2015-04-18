<?php
class CheckboxDisplay extends ItemDisplay {
	var $type = "list";
	var $template = "multiple_select";
	public function build_display($options) {
		$this->attributes['class'][] = "multiple_select";
		if (!empty($options['model']) && !empty($options['action'])) {
			$this->model = $options['model'];
			$this->action = $options['action'];
		} else if (!empty($options['options'])) {
			foreach ($options['options'] as $option) {
				$this->items[] = array("id" => $option, "label" => $option);
			}
		}
	}
}
?>
