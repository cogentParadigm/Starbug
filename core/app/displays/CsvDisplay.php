<?php
class CsvDisplay extends ItemDisplay {
	public $template = "csv";
	function build_display($options) {
		$this->model = $options['model'];
		$this->action = $options['action'];
		sb($this->model)->build_display($display);
	}
	function query($options=null, $model="") {
		if (isset($this->options['data'])) $this->items = $this->options['data'];
		else parent::query($options, $model);
	}
}
?>
