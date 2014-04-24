<?php
class hook_store_upload {
	var $uploaded = false;
	function empty_validate($query, $column, $argument) {
		if (!empty($_FILES[$column])) $query->set($column, $this->store($query, $column, "", $column, $argument));
	}
	function store(&$query, $key, $value, $column, $argument) {
		if ($this->uploaded) return $value;
		else $this->uploaded = true;
		$record = array("filename" => "", "mime_type" => "", "caption" => "$name $field");
		if (!empty($value) && is_numeric($value)) $records['id'] = $value;
		$file = $_FILES[$column];
		if (!empty($file['name'])) {
			sb("files")->upload($record, $file);
			if (!errors("files")) {
				$value = (empty($record['id'])) ? sb("files")->insert_id : $record['id'];
			} else {
				foreach(errors("files[filename]", true) as $type => $message) error($message, $column);
			}
		}
		return $value;
	}
}
?>
