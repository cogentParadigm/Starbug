<?php
foreach($args as $field => $upload) {
	$record = array("filename" => "", "mime_type" => "", "caption" => "$name $field");
	if (!empty($fields[$field])) $records['id'] = $fields[$field];
	$file = $_FILES[$upload];
	if (!empty($file['name'])) {
		$file_errors = $this->get("files")->upload($record, $file);
    if (empty($file_errors)) {
        $fields[$field] = (empty($record['id'])) ? $this->insert_id : $record['id'];
        unset($errors[$field]['required']);
    } else {
        foreach($file_errors['filename'] as $type => $message) $errors[$field][$type] = $message;
    }
	}
}
?>
