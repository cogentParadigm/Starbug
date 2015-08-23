<?php
class ImportsForm extends FormDisplay {
	public $source_keys = array();
	public $source_values = array();
	public $model = "imports";
	public $cancel_url = "admin/imports";
	function build_display($options) {
		$source = $this->get("source");
		$model = $this->get("model");
		$this->add("name", "model  input_type:select  from:entities  query:model", "source  input_type:file_select");
		if (!empty($source) && !empty($model)) {
			$this->add(array("fields",
				"input_type" => "crud",
				"data-dojo-props" => array(
					"get_data" => "{model:'".$model."',  source:'".$source."'}"
				)
			));
		}
	}
}
?>
