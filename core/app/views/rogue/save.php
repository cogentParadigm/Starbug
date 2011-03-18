<?php
	$open = $_POST['open'];
	$old = $_POST['old'];
	$new = $_POST['new'];
	$output = array();
	$contents = file_get_contents(BASE_DIR."/$open");
	while (false !== strpos($old, "\n  ")) $old = str_replace("\n  ", "\n\t", $old);
	while (false !== strpos($new, "\n  ")) $new = str_replace("\n  ", "\n\t", $new);
	while (false !== strpos($old, "\t  ")) $old = str_replace("\t  ", "\t\t", $old);
	while (false !== strpos($new, "\t  ")) $new = str_replace("\t  ", "\t\t", $new);
	if ($contents != $old) { // FILE ON DISK HAS CHANGED
		$output['status'] = 'changed on disk';
	} else { // FILE SEEMS SAFE TO EDIT
		$file = fopen(BASE_DIR."/$open", "wb");
		fwrite($file, $new);
		fclose($file);
		$output['status'] = 'saved';
	}
	header("Content-Type: application/json");
	echo json_encode($output);
?>