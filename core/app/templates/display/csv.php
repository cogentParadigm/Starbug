<?php
	$out = fopen('php://output', 'w');
	$row = array();
	foreach ($display->fields as $name => $field) $row[] = $field['label'];
	fputcsv($out, $row);
	foreach ($display->items as $item) {
		$row = array();
		foreach ($display->fields as $field_name => $field) $row[] = $item[$field_name];
		fputcsv($out, $row);
	}
	fclose($out);
?>
