<?php
foreach($args as $field => $upload) {
	$record = array("filename" => "", "mime_type" => "", "caption" => "");
	if (!empty($fields[$field])) $records['id'] = $fields[$field];
	$file = $_FILES[$upload];
	$file_errors = $this->get("files")->upload($record, $file);
	foreach($file_errors['filename'] as $type => $message) $errors[$field][$type] = $message;
	$fields[$field] = (empty($record['id'])) ? $this->insert_id : $record['id'];
}
?>
