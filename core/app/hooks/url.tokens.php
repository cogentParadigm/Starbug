<?php
	foreach ($tokens as $path => $token) {
		$replacements[$token] = isset($data['url_flags']) ? uri($path, $data['url_flags']) : uri($path);
	}
?>
