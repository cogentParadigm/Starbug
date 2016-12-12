<?php
namespace Starbug\Core;
class Imports_fieldsForm extends FormDisplay {
	public $source_keys = array();
	public $source_values = array();
	public $model = "imports_fields";
	public $cancel_url = "admin/imports_fields";
	function build_display($options) {
		$data = $this->request->getPost();
		if ($this->success("create") && empty($data['imports_fields']['id'])) {
			$this->request->setPost('imports_fields', 'id', $this->models->get($this->model)->insert_id);
		}
		$this->parse_source($options['source']);
		$this->add(array("source", "input_type" => "select", "options" => $this->source_values));
		$model = $this->models->get($options['model']);
		$dest_ops = array("destination", "input_type" => "select");
		if (method_exists($model, "import_fields")) {
			$dest_ops = array_merge($dest_ops, $model->import_fields($options));
		} else {
			$dest_ops['options'] = array_keys($this->models->get($options['model'])->column_info());
		}
		$this->add($dest_ops);
		$this->add(["update_key", "input_type" => "checkbox", "label" => "Use this field as a key to update records"]);
	}
	function parse_source($id) {
		$file = $this->models->get("files")->load($id);
		$head = array();
		if (false !== ($handle = fopen("app/public/uploads/".$file['id']."_".$file['filename'], "r"))) {
			$head = fgetcsv($handle);
		}
		$this->source_keys = array_keys($head);
		$this->source_values = array_values($head);
	}
}
?>
