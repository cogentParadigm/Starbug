<?php
	foreach ($tokens as $name => $token) {
		if ($name === "token") {
			$content_type = query("content_types")->condition("type", $data['uris']['type'])->one();
			$pattern = (empty($content_type['url_pattern'])) ? "[uris:path]" : $content_type['url_pattern'];
			$replacements[$token] = token_replace($pattern, $data);
		}
	}
?>
