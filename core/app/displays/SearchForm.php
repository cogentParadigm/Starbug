<?php
class SearchForm extends FormDisplay {
	public $method = "get";
	public $default_action = "search";
	function build_display($options) {
		$this->attributes['class'][] = 'form-inline';
		$this->add("keywords  input_type:text  nolabel:");
		$this->actions->add("search  class:btn-default");
		$this->actions->template = "inline";
	}
}
?>
