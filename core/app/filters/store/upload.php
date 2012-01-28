<?php
foreach($args as $field => $upload) {
	$record = array("filename" => "", "mime_type" => "", "caption" => "$name $field");
	if (!empty($fields[$field])) $records['id'] = $fields[$field];
	$file = $_FILES[$field];
	if (!empty($file['name'])) {
		$this->get("files")->upload($record, $file);
    if (if (!errors("files")) {
        $fields[$field] = (empty($record['id'])) ? $this->insert_id : $record['id'];
        unset($errors[$field]['required']);
    } else {
        foreach($errors("files[filename]", true) as $type => $message) $errors[$field][$type] = $message;
    }
	}
}
?>
