<?php
namespace Starbug\Core;
class CsvDisplay extends ItemDisplay {
	public $template = "csv";
	function build_display($options) {
		$this->model = $options['model'];
		$this->action = $options['action'];
		$this->models->get($this->model)->build_display($this);
	}
	function query($options = null) {
		if (isset($this->options['data'])) $this->items = $this->options['data'];
		else parent::query($options);
	}
}
?>
