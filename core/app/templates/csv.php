<?php
	$out = fopen('php://output', 'w');
	foreach ($response->content as $item) {
		fputcsv($out, $item);
	}
	fclose($out);
?>
