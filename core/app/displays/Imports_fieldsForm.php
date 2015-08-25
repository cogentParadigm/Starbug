<?php
class Imports_fieldsForm extends FormDisplay {
	public $source_keys = array();
	public $source_values = array();
	public $model = "imports_fields";
	public $cancel_url = "admin/imports_fields";
	function build_display($options) {
		if ($this->success("create") && empty($this->request->data['imports_fields']['id'])) {
			$this->request->data['imports_fields']['id'] = $this->models->get($this->model)->insert_id;
		}
		$this->parse_source($options['source']);
		$this->add(array("source", "input_type" => "select", "options" => $this->source_values));
		$this->add(array("destination", "input_type" => "select", "options" => array_keys(column_info($options['model']))));
		$this->add("update_key  input_type:checkbox  label:Use this field as a key to update records");
	}
	function parse_source($id) {
		$file = query("files")->condition("id", $id)->one();
		$head = array();
		if (false !== ($handle = fopen("app/public/uploads/".$file['id']."_".$file['filename'], "r"))) {
			$head = fgetcsv($handle);
		}
		$this->source_keys = array_keys($head);
		$this->source_values = array_values($head);
	}
}
?>
