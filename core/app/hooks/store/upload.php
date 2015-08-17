<?php
class hook_store_upload extends QueryHook {
	var $uploaded = false;
	protected $request;
	protected $models;
	protected $files;
	function __construct(ModelFactoryInterface $models, Request $request) {
		$this->request = $request;
		$this->models = $models;
		$this->files = $models->get("files");
	}
	function empty_validate($query, $column, $argument) {
		if (!empty($this->request->files[$column]["name"])) $query->set($column, $this->store($query, $column, "", $column, $argument));
	}
	function store(&$query, $key, $value, $column, $argument) {
		if ($this->uploaded) return $value;
		else $this->uploaded = true;
		$record = array("filename" => "", "mime_type" => "", "caption" => "$name $field");
		if (!empty($value) && is_numeric($value)) $records['id'] = $value;
		$file = $this->request->files[$column];
		if (!empty($file['name'])) {
			$this->files->upload($record, $file);
			if (!$this->files->errors()) {
				$value = (empty($record['id'])) ? $this->files->insert_id : $record['id'];
			} else {
				foreach($this->files->errors("filename", true) as $type => $message) $this->models->get($query->model)->error($message, $column);
			}
		}
		return $value;
	}
}
?>
